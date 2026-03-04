<?php

namespace App\Service;

use App\Entity\Challenge;

class ChallengeValidatorService
{
    // Liste des niveaux autorisés
    private const VALID_LEVELS = ['Débutant', 'Intermédiaire', 'Avancé'];
    
    /**
     * Règle 1: Le titre ne doit pas être vide
     */
    public function isTitleValid(Challenge $challenge): bool
    {
        $titre = $challenge->getTitre();
        return $titre !== null && trim($titre) !== '';
    }
    
    /**
     * Règle 2: La description ne doit pas être vide
     */
    public function isDescriptionValid(Challenge $challenge): bool
    {
        $description = $challenge->getDescription();
        return $description !== null && trim($description) !== '';
    }
    
    /**
     * Règle 3: La durée doit être > 0
     */
    public function isDurationValid(Challenge $challenge): bool
    {
        $duree = $challenge->getDuree();
        return is_int($duree) && $duree > 0;
    }
    
    /**
     * Règle 4: La date de fin doit être après la date de début
     */
    public function isDateRangeValid(Challenge $challenge): bool
    {
        $dateDebut = $challenge->getDateDebut();
        $dateFin = $challenge->getDateFin();
        
        if (!$dateDebut || !$dateFin) {
            return false;
        }
        
        return $dateFin > $dateDebut;
    }
    
    /**
     * Règle 5: Le niveau doit être dans la liste des valeurs autorisées
     * (Peut être null ou vide - dans ce cas, la validation échoue car ce n'est pas une valeur autorisée)
     */
    public function isLevelValid(Challenge $challenge): bool
    {
        $niveau = $challenge->getNiveau();
        
        // Si le niveau est null ou vide, ce n'est pas une valeur autorisée
        if ($niveau === null || trim($niveau) === '') {
            return false;
        }
        
        return in_array($niveau, self::VALID_LEVELS, true);
    }
    
    /**
     * Méthode combinée qui vérifie toutes les règles
     */
    public function isValid(Challenge $challenge): bool
    {
        return $this->isTitleValid($challenge)
            && $this->isDescriptionValid($challenge)
            && $this->isDurationValid($challenge)
            && $this->isDateRangeValid($challenge)
            && $this->isLevelValid($challenge);
    }
    
    /**
     * Retourne les erreurs de validation sous forme de tableau
     */
    public function getErrors(Challenge $challenge): array
    {
        $errors = [];
        
        if (!$this->isTitleValid($challenge)) {
            $errors[] = "Le titre ne peut pas être vide";
        }
        
        if (!$this->isDescriptionValid($challenge)) {
            $errors[] = "La description ne peut pas être vide";
        }
        
        if (!$this->isDurationValid($challenge)) {
            $errors[] = "La durée doit être un nombre entier positif";
        }
        
        if (!$this->isDateRangeValid($challenge)) {
            $errors[] = "La date de fin doit être postérieure à la date de début";
        }
        
        if (!$this->isLevelValid($challenge)) {
            $validLevels = implode(', ', self::VALID_LEVELS);
            $errors[] = "Le niveau doit être l'un des suivants : {$validLevels}";
        }
        
        return $errors;
    }
    
    /**
     * Retourne la liste des niveaux valides
     */
    public function getValidLevels(): array
    {
        return self::VALID_LEVELS;
    }
}