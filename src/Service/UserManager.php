<?php

namespace App\Service;

use App\Entity\User;

class UserManager
{
    /**
     * Valide les données d'un utilisateur selon les règles métier
     * 
     * Règles métier:
     * 1. Le nom est obligatoire
     * 2. Le prénom est obligatoire
     * 3. L'email doit être valide
     * 4. Le rôle doit être valide (ETUDIANT, ENSEIGNANT, ADMIN)
     * 5. Pour un étudiant, le niveau est obligatoire
     * 
     * @param User $user
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(User $user): bool
    {
        // Règle 1: Le nom est obligatoire
        if (empty($user->getNom())) {
            throw new \InvalidArgumentException('Le nom est obligatoire');
        }

        // Règle 2: Le prénom est obligatoire
        if (empty($user->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom est obligatoire');
        }

        // Règle 3: L'email doit être valide
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalide');
        }

        // Règle 4: Le rôle doit être valide
        $rolesValides = ['ETUDIANT', 'ENSEIGNANT', 'ADMIN'];
        if (!in_array($user->getRole(), $rolesValides)) {
            throw new \InvalidArgumentException('Le rôle doit être ETUDIANT, ENSEIGNANT ou ADMIN');
        }

        // Règle 5: Pour un étudiant, le niveau est obligatoire
        if ($user instanceof \App\Entity\Etudiant) {
            if (empty($user->getNiveau())) {
                throw new \InvalidArgumentException('Le niveau est obligatoire pour un étudiant');
            }
            
            $niveauxValides = ['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'];
            if (!in_array($user->getNiveau(), $niveauxValides)) {
                throw new \InvalidArgumentException('Le niveau doit être DEBUTANT, INTERMEDIAIRE ou AVANCE');
            }
        }

        return true;
    }

    /**
     * Vérifie si un utilisateur peut être suspendu
     * 
     * Règles métier:
     * 1. Un utilisateur déjà suspendu ne peut pas être suspendu à nouveau
     * 2. Un administrateur ne peut pas être suspendu
     * 
     * @param User $user
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function canBeSuspended(User $user): bool
    {
        // Règle 1: Vérifier si déjà suspendu
        if ($user->getIsSuspended()) {
            throw new \InvalidArgumentException('L\'utilisateur est déjà suspendu');
        }

        // Règle 2: Un admin ne peut pas être suspendu
        if ($user->getRole() === 'ADMIN') {
            throw new \InvalidArgumentException('Un administrateur ne peut pas être suspendu');
        }

        return true;
    }
}
