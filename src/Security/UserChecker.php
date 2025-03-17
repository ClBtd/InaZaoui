<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    // Vérification avant l'authentification
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérifie que l'utilisateur est bien de type User
        if (!$user instanceof \App\Entity\User) {
            throw new \InvalidArgumentException('Type d\'utilisateur invalide.');
        }

        // Vérifie si l'utilisateur a l'accès autorisé
        if (!$user->isAccess()) {
            throw new CustomUserMessageAccountStatusException('Votre compte est désactivé.');
        }
    }

    // Vérification après l'authentification (aucune vérification spécifique dans ce cas)
    public function checkPostAuth(UserInterface $user): void
    {
    }
}
