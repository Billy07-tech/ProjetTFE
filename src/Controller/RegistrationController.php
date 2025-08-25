<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifie que l'email n'existe pas déjà
            if ($em->getRepository(Utilisateur::class)->findOneBy(['email' => $utilisateur->getEmail()])) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // Hachage du mot de passe
            $utilisateur->setPassword($passwordHasher->hashPassword(
                $utilisateur,
                $form->get('password')->getData()
            ));

            $utilisateur->setRoles(['ROLE_USER']);
            $utilisateur->setIsVerified(true); // DIRECTEMENT vérifié

            // Persistance
            $em->persist($utilisateur);
            $em->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez vous connecter immédiatement.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
