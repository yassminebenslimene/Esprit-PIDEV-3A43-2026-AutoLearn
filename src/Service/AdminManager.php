<?php

namespace App\Service;

use App\Entity\Admin;

class AdminManager
{
    /**
     * Valide les données d'un administrateur selon les règles métier
     * 
     * Règles métier pour Admin:
     * 1. Le nom est obligatoire (2-50 caractères, lettres uniquement)
     * 2. Le prénom est obligatoire (2-50 caractères, lettres uniquement)
     * 3. L'email doit être valide et unique
     * 4. Le mot de passe doit respecter les critères de sécurité
     * 
     * @param Admin $admin
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(Admin $admin): bool
    {
        // Règle 1: Le nom est obligatoire et doit respecter le format
        if (empty($admin->getNom())) {
            throw new \InvalidArgumentException('Le nom est obligatoire');
        }
        
        if (strlen($admin->getNom()) < 2) {
            throw new \InvalidArgumentException('Le nom doit contenir au moins 2 caractères');
        }
        
        if (strlen($admin->getNom()) > 50) {
            throw new \InvalidArgumentException('Le nom ne peut pas dépasser 50 caractères');
        }
        
        if (preg_match("/\d/", $admin->getNom())) {
            throw new \InvalidArgumentException('Le nom ne peut pas contenir de chiffres');
        }
        
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s']+$/u", $admin->getNom())) {
            throw new \InvalidArgumentException('Le nom ne peut contenir que des lettres, espaces et apostrophes');
        }

        // Règle 2: Le prénom est obligatoire et doit respecter le format
        if (empty($admin->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom est obligatoire');
        }
        
        if (strlen($admin->getPrenom()) < 2) {
            throw new \InvalidArgumentException('Le prénom doit contenir au moins 2 caractères');
        }
        
        if (strlen($admin->getPrenom()) > 50) {
            throw new \InvalidArgumentException('Le prénom ne peut pas dépasser 50 caractères');
        }
        
        if (preg_match("/\d/", $admin->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom ne peut pas contenir de chiffres');
        }
        
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s']+$/u", $admin->getPrenom())) {
            throw new \InvalidArgumentException('Le prénom ne peut contenir que des lettres, espaces et apostrophes');
        }

        // Règle 3: L'email doit être valide
        if (empty($admin->getEmail())) {
            throw new \InvalidArgumentException('L\'email est obligatoire');
        }
        
        if (!filter_var($admin->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('L\'email n\'est pas valide');
        }
        
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $admin->getEmail())) {
            throw new \InvalidArgumentException('Format d\'email invalide');
        }

        return true;
    }

    /**
     * Vérifie si un administrateur peut être suspendu
     * 
     * Règles métier:
     * 1. Un administrateur ne peut JAMAIS être suspendu
     * 
     * @param Admin $admin
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function canBeSuspended(Admin $admin): bool
    {
        throw new \InvalidArgumentException('Un administrateur ne peut pas être suspendu');
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
