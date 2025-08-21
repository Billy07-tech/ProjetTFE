<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use App\Repository\AssistanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
    public function assistance(AssistanceRepository $assistanceRepository): Response
    {
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

   #[Route('/utilisateur/supprimer/{id}', name: 'app_utilisateur_supprimer', methods: ['POST'])]
    public function supprimerUtilisateur(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        // Empêche l'admin de se supprimer lui-même
        if ($utilisateur === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_utilisateur');
        }

        if (!$this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $entityManager->remove($utilisateur);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_utilisateur');
    }
}
