<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AssistanceController extends AbstractController
{
    #[Route('/assistance', name: 'app_assistance')]
    public function index(): Response
    {
        return $this->render('assistance/index.html.twig', [
            'controller_name' => 'AssistanceController',
        ]);
    }
}
