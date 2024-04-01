<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use App\Repository\AssistanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
     * @Route("/admin", name="app_administrateur")
     * @IsGranted("ROLE_ADMIN")
     */

#[Route('/administrateur')]
class AdministrateurController extends AbstractController
{
    #[Route('/', name: 'app_administrateur')]
    public function index(): Response
    {
        
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès refusé.');
        }

        return $this->render('administrateur/index.html.twig');
    }

    #[Route('/assistance', name: 'app_demande_assistance')]
    public function assistance(AssistanceRepository $assistanceRepository){

        return $this->render('administrateur/liste_demandes_assistance.html.twig', [
            'demandesAssistances' => $assistanceRepository->findAll(),
        ]);
    }

    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function utilisateur(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('administrateur/utilisateur.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }
}

