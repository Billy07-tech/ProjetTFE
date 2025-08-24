<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StripeService;

#[Route('/paiement')]
class PaiementController extends AbstractController
{
    #[Route('/checkout', name: 'paiement_checkout')]
    public function checkout(StripeService $stripeService): Response
    {
        $user = $this->getUser();
        if (!$user || !$user->getPanier()) {
            return $this->redirectToRoute('panier_index');
        }

        $items = $user->getPanier()->getItems()->toArray();

        $session = $stripeService->createCheckoutSession(
            $items,
            $this->generateUrl('paiement_success', [], 0),
            $this->generateUrl('panier_index', [], 0)
        );

        return $this->redirect($session->url);
    }

    #[Route('/success', name: 'paiement_success')]
    public function success(): Response
    {
        return $this->render('paiement/success.html.twig');
    }
}
