<?php
namespace App\Controller;

use App\Entity\Panier;
use App\Entity\PanierItem;
use App\Entity\Commande;
use App\Entity\CommandeItem;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'panier_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $panier = $user->getPanier();
        return $this->render('panier/index.html.twig', ['panier' => $panier]);
    }

    #[Route('/add/{id}', name: 'panier_add')]
    public function add(int $id, ProduitRepository $produitRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $produit = $produitRepo->find($id);
        if (!$produit) return $this->redirectToRoute('panier_index');

        $panier = $user->getPanier() ?? (new Panier())->setUtilisateur($user);

        $item = null;
        foreach ($panier->getItems() as $i) {
            if ($i->getProduit()->getId() === $id) { $item = $i; break; }
        }

        if ($item) {
            $item->setQuantite($item->getQuantite() + 1);
        } else {
            $item = (new PanierItem())->setProduit($produit)->setQuantite(1);
            $panier->addItem($item);
        }

        $em->persist($panier);
        $em->flush();

        return $this->redirectToRoute('panier_index');
    }

    #[Route('/remove/{id}', name: 'panier_remove')]
    public function remove(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $panier = $user?->getPanier();
        if (!$panier) return $this->redirectToRoute('panier_index');

        foreach ($panier->getItems() as $item) {
            if ($item->getProduit()->getId() === $id) {
                $panier->removeItem($item);
                $em->remove($item);
                break;
            }
        }

        $em->flush();
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/clear', name: 'panier_clear')]
    public function clear(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $panier = $user?->getPanier();
        if ($panier) {
            foreach ($panier->getItems() as $item) $em->remove($item);
            $em->remove($panier);
            $em->flush();
        }
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/increase/{id}', name: 'panier_increase')]
    public function increase(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $panier = $user?->getPanier();
        if (!$panier) return $this->redirectToRoute('panier_index');

        foreach ($panier->getItems() as $item) {
            if ($item->getProduit()->getId() === $id) {
                $item->setQuantite($item->getQuantite() + 1);
                break;
            }
        }

        $em->flush();
        return $this->redirectToRoute('panier_index');
    }

    #[Route('/decrease/{id}', name: 'panier_decrease')]
    public function decrease(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $panier = $user?->getPanier();
        if (!$panier) return $this->redirectToRoute('panier_index');

        foreach ($panier->getItems() as $item) {
            if ($item->getProduit()->getId() === $id) {
                $item->setQuantite($item->getQuantite() - 1);
                if ($item->getQuantite() <= 0) {
                    $panier->removeItem($item);
                    $em->remove($item);
                }
                break;
            }
        }

        $em->flush();
        return $this->redirectToRoute('panier_index');
    }

    // Paiement Stripe
    #[Route('/checkout', name: 'panier_checkout')]
    public function checkout(\App\Service\StripeService $stripeService)
    {
        $user = $this->getUser();
        if (!$user || !$user->getPanier() || count($user->getPanier()->getItems()) === 0) {
            return $this->redirectToRoute('panier_index');
        }

        $lineItems = [];
        foreach ($user->getPanier()->getItems() as $item) {
            $produit = $item->getProduit();
            if (!$produit) continue;

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => intval($produit->getPrix() * 100),
                    'product_data' => ['name' => $produit->getNom()],
                ],
                'quantity' => $item->getQuantite(),
            ];
        }

        $successUrl = $this->generateUrl('panier_succes', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('panier_index', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);

        $session = $stripeService->createCheckoutSession($lineItems, $successUrl, $cancelUrl, [
            'shipping_address_collection' => ['allowed_countries' => ['FR', 'BE', 'CH']],
        ]);

        return $this->redirect($session->url, 303);
    }

    // Page succès après paiement
    #[Route('/succes', name: 'panier_succes')]
    public function succes(EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('panier_index');

        $panier = $user->getPanier();
        if (!$panier || count($panier->getItems()) === 0) {
            return $this->redirectToRoute('panier_index');
        }

        $commande = new Commande();
        $commande->setUtilisateur($user);
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus('payée');

        $total = 0;
        foreach ($panier->getItems() as $item) {
            $produit = $item->getProduit();
            $quantite = $item->getQuantite();
            $prix = $produit->getPrix();

            $commandeItem = new CommandeItem();
            $commandeItem->setProduit($produit);
            $commandeItem->setQuantite($quantite);
            $commandeItem->setPrix($prix);
            $commandeItem->setCommande($commande);

            $commande->addItem($commandeItem);
            $em->persist($commandeItem);

            $total += $prix * $quantite;
        }
        $commande->setTotal($total);
        $em->persist($commande);

        // Vider le panier après création de la commande
        foreach ($panier->getItems()->toArray() as $item) {
            $panier->removeItem($item);
            $em->remove($item);
        }

        $em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@tonsite.com', 'Mon Site'))
            ->to($user->getEmail())
            ->subject('Confirmation de votre commande #' . $commande->getId())
            ->htmlTemplate('emails/commande_confirmation.html.twig')
            ->context([
                'user' => $user,
                'commande' => $commande,
                'items' => $commande->getItems(),
                'total' => $total,
            ]);
        $mailer->send($email);

        return $this->render('panier/succes.html.twig', [
            'commande' => $commande,
            'items' => $commande->getItems(),
            'total' => $total
        ]);
    }
}
