<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
     * @Route("/admin", name="app_administrateur")
     * @IsGranted("ROLE_ADMIN")
     */
class AdministrateurController extends AbstractController
{
    #[Route('/administrateur', name: 'app_administrateur')]

    public function index(): Response
    {
        // Vérifier si l'utilisateur a le rôle administrateur
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Si l'utilisateur n'a pas le rôle administrateur, lui refuser l'accès
            throw new AccessDeniedException('Accès refusé.');
        }

        // Si l'utilisateur a le rôle administrateur, afficher la page d'administration
        return $this->render('administrateur/index.html.twig');
    }
}

