<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Repository\CompetitionRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CompetitionController extends AbstractController
{
    #[Route('/calendrier', name: 'competition_calendrier')]
    public function calendrier(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('competition/calendrier.html.twig');
    }

    #[Route('/api/competitions', name: 'competition_api')]
    public function api(Request $request, CompetitionRepository $repo): JsonResponse
    {
        $comps = $repo->findAll();

        $events = [];
        foreach ($comps as $c) {
            $events[] = [
                'id' => $c->getId(),
                'title' => $c->getTitre(),
                'start' => $c->getDateDebut()->format('c'),
                'end' => $c->getDateFin()->format('c'),
                'url' => $this->generateUrl('competition_show', ['id' => $c->getId()])
            ];
        }
        return $this->json($events);
    }


    #[Route('/competition/{id<\d+>}', name: 'competition_show')]
    public function show($id, CompetitionRepository $repo): Response
    {
        $comp = $repo->find($id);

        if (!$comp) {
            throw $this->createNotFoundException("La compétition n’existe pas (id=$id).");
        }

        return $this->render('competition/show.html.twig', [
            'comp' => $comp
        ]);
    }

    #[Route('/competition/new', name: 'competition_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $comp = new Competition();
        $form = $this->createForm(CompetitionType::class, $comp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comp);
            $em->flush();

            $this->addFlash('success', 'Compétition créée avec succès !');

            return $this->redirectToRoute('competition_calendrier');
        }

        return $this->render('competition/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/competition/{id}/edit', name: 'competition_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Competition $comp, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CompetitionType::class, $comp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Compétition modifiée avec succès !');
            return $this->redirectToRoute('competition_calendrier');
        }

        return $this->render('competition/edit.html.twig', [
            'comp' => $comp,
            'form' => $form->createView(),
        ]);
    }

    // ⚠️ {id} contraint à numérique pour éviter les collisions de routes
    #[Route('/competition/{id<\d+>}/delete', name: 'competition_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Competition $comp, EntityManagerInterface $em): Response
    {
        // Vérif CSRF
        if (!$this->isCsrfTokenValid('delete_competition_' . $comp->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Action interdite (token CSRF invalide).');
            return $this->redirectToRoute('competition_calendrier');
        }

        $em->remove($comp);
        $em->flush();

        $this->addFlash('success', 'Compétition supprimée avec succès.');
        return $this->redirectToRoute('competition_calendrier');
    }
}
