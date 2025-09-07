<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Competition;
use App\Repository\UtilisateurRepository;
use App\Repository\AssistanceRepository;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdministrateurController extends AbstractController
{
    // Tableau de bord
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('administrateur/index.html.twig');
    }

    #[Route('/app_demande_assistance', name: 'assistance', methods: ['GET'])]
    public function assistance(AssistanceRepository $assistanceRepository): Response
    {
        return $this->render('administrateur/liste_demandes_assistance.html.twig', [
            'demandesAssistances' => $assistanceRepository->findAll(),
        ]);
    }

    // Liste des utilisateurs
    #[Route('/app_utilisateur', name: 'users', methods: ['GET'])]
    public function users(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));

        $qb = $utilisateurRepository->createQueryBuilder('u');
        if ($q !== '') {
            $qb->andWhere('LOWER(u.pseudo) LIKE :q OR LOWER(u.email) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        $utilisateurs = $qb->orderBy('u.id', 'DESC')->getQuery()->getResult();

        return $this->render('administrateur/utilisateur.html.twig', [
            'utilisateurs' => $utilisateurs,
            'q'            => $q,
        ]);
    }
    // Suppression d'un utilisateur (CSRF + on empêche l'auto-suppression)
    #[Route('/utilisateurs/{id<\d+>}/delete', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        if ($utilisateur === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_users');
        }

        if (!$this->isCsrfTokenValid('delete' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $em->remove($utilisateur);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('admin_users');
    }

    // Compétitions : liste + petite recherche
    #[Route('/competitions', name: 'competition_index', methods: ['GET'])]
    public function competitionsIndex(Request $request, CompetitionRepository $repo): Response
    {
        $q = trim((string) $request->query->get('q', ''));

        if ($q !== '') {
            $competitions = $repo->createQueryBuilder('c')
                ->where('LOWER(c.titre) LIKE :q OR LOWER(c.lieu) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($q) . '%')
                ->orderBy('c.dateDebut', 'DESC')
                ->getQuery()->getResult();
        } else {
            $competitions = $repo->createQueryBuilder('c')
                ->orderBy('c.dateDebut', 'DESC')
                ->getQuery()->getResult();
        }

        return $this->render('administrateur/competition.html.twig', [
            'competitions' => $competitions,
            'q'            => $q,
        ]);
    }


    // Suppression d'une compétition (CSRF)
    #[Route('/competitions/{id<\d+>}/delete', name: 'competition_delete', methods: ['POST'])]
    public function deleteCompetition(Request $request, Competition $comp, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_competition_' . $comp->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action interdite (token CSRF invalide).');
            return $this->redirectToRoute('admin_competition_index');
        }

        $em->remove($comp);
        $em->flush();

        $this->addFlash('success', 'Compétition supprimée.');
        return $this->redirectToRoute('admin_competition_index');
    }
}
