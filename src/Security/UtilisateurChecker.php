<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UtilisateurChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Si l'utilisateur n'a pas confirmé son email
        if (!$user->getIsVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'Vous devez valider votre compte par email avant de vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Ici rien à faire pour l’instant
    }
}
