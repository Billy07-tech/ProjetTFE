<?php

namespace App\Controller;

use App\Entity\Assistance;
use App\Form\AssistanceType;
use App\Repository\AssistanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AssistanceController extends AbstractController
{
    #[Route('/assistance', name: 'app_assistance')]
    public function index(Request $request,EntityManagerInterface $entityManager,AssistanceRepository $assistanceRepository): Response
    {
        $assistance = new Assistance();
        $form = $this->createForm(AssistanceType::class, $assistance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $assistance->setDateEnvoie(new \DateTime()); 
            $entityManager->persist($assistance);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a été envoyée avec succès !');

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('assistance/index.html.twig', [
            'assistance' => $assistanceRepository->findAll(),
            'formulaire' => $form
        ]);
    }
}
