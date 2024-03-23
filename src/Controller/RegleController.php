<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegleController extends AbstractController
{
    #[Route('/regle', name: 'app_regle')]
    public function index(): Response
    {
        return $this->render('regle/index.html.twig', [
            'controller_name' => 'RegleController',
        ]);
    }
}
