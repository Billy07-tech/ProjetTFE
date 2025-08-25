<?php
namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/historique', name: 'commande_historique')]
    public function historique(CommandeRepository $commandeRepo): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $commandes = $commandeRepo->findBy(
            ['utilisateur' => $user],
            ['dateCommande' => 'DESC']
        );

        return $this->render('commande/historique.html.twig', [
            'commandes' => $commandes
        ]);
    }
}
