<?php

namespace App\Service;

use App\Entity\Etudiant;

class EtudiantManager
{
    /**
     * Valide les données d'un étudiant selon les règles métier
     * 
     * Règles métier pour Etudiant:
     * 1. Le nom est obligatoire (2-50 caractères, lettres uniquement)
     * 2. Le prénom est obligatoire (2-50 caractères, lettres uniquement)
     * 3. L'email doit être valide et unique
     * 4. Le niveau est obligatoire (DEBUTANT, INTERMEDIAIRE, AVANCE)
     * 5. Le mot de passe doit respecter les critères de sécurité
     * 
     * @param Etudiant $etudiant
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(Etudiant $etudiant): bool
    {
        // Règle 1: Le nom est obligatoire et doit respecter le format
        if (empty($etudiant->getNom())) {
            throw new \InvalidArgumentException('Le nom est obligatoire');
        }
        
        if (strlen($etudiant->getNom()) < 2) {
            throw new \InvalidArgumentException('Le nom doit contenir au moins 2 caractères');
        }
        
        if (strlen($etudiant->getNom()) > 50) {
            throw new \InvalidArgumentException('Le nom ne peut pas dépasser 50 caractères');
        }
        
        if (preg_match("/\d/", $etudiant->getNom())) {
            throw new \InvalidArgumentException('Le nom ne peut pas contenir de chiffres');
        }
        
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s']+$/u", $etudiant->getNom())) {
            throw new \InvalidArgumentException('Le nom ne peut contenir que des lettres, espaces et apostrophes');
        }

        // Règle 2: Le prénom est obligatoire et doit respecter le format
        if (empty($etudiant->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom est obligatoire');
        }
        
        if (strlen($etudiant->getPrenom()) < 2) {
            throw new \InvalidArgumentException('Le prénom doit contenir au moins 2 caractères');
        }
        
        if (strlen($etudiant->getPrenom()) > 50) {
            throw new \InvalidArgumentException('Le prénom ne peut pas dépasser 50 caractères');
        }
        
        if (preg_match("/\d/", $etudiant->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom ne peut pas contenir de chiffres');
        }
        
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s']+$/u", $etudiant->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom ne peut contenir que des lettres, espaces et apostrophes');
        }

        // Règle 3: L'email doit être valide
        if (empty($etudiant->getEmail())) {
            throw new \InvalidArgumentException('L\'email est obligatoire');
        }
        
        if (!filter_var($etudiant->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('L\'email n\'est pas valide');
        }
        
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $etudiant->getEmail())) {
            throw new \InvalidArgumentException('Format d\'email invalide');
        }

        // Règle 4: Le niveau est obligatoire pour un étudiant
        if (empty($etudiant->getNiveau())) {
            throw new \InvalidArgumentException('Le niveau est obligatoire pour un étudiant');
        }
        
        $niveauxValides = ['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'];
        if (!in_array($etudiant->getNiveau(), $niveauxValides)) {
            throw new \InvalidArgumentException('Le niveau doit être DEBUTANT, INTERMEDIAIRE ou AVANCE');
        }

        return true;
    }

    /**
     * Vérifie si un étudiant peut être suspendu
     * 
     * Règles métier:
     * 1. Un étudiant déjà suspendu ne peut pas être suspendu à nouveau
     * 
     * @param Etudiant $etudiant
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function canBeSuspended(Etudiant $etudiant): bool
    {
        if ($etudiant->getIsSuspended()) {
            throw new \InvalidArgumentException('L\'étudiant est déjà suspendu');
        }

        return true;
    }

    /**
     * Valide le mot de passe selon les critères de sécurité
     * 
     * @param string $password
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validatePassword(string $password): bool
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('Le mot de passe est obligatoire');
        }
        
        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins 6 caractères');
        }
        
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/", $password)) {
            throw new \InvalidArgumentException('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');
        }

        return true;
    }
}
