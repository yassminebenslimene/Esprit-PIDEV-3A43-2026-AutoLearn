<?php

namespace App\Service;

use App\Entity\Evenement;

/**
 * Service de gestion des règles métier pour l'entité Evenement
 */
class EvenementManager
{
    /**
     * Valide un événement selon les règles métier
     * 
     * Règles métier:
     * 1. Le titre doit contenir au moins 5 caractères
     * 2. La description doit contenir au moins 10 caractères
     * 3. Le lieu ne doit pas être vide
     * 4. La date de fin doit être postérieure à la date de début
     * 5. Le nombre maximum d'équipes doit être entre 1 et 100
     * 6. La date de début doit être dans le futur
     * 
     * @param Evenement $evenement L'événement à valider
     * @return bool True si l'événement est valide
     * @throws \InvalidArgumentException Si une règle métier n'est pas respectée
     */
    public function validate(Evenement $evenement): bool
    {
        // Règle 1: Titre minimum 5 caractères
        if (empty($evenement->getTitre())) {
            throw new \InvalidArgumentException('Le titre est obligatoire');
        }
        
        if (strlen($evenement->getTitre()) < 5) {
            throw new \InvalidArgumentException(
                'Le titre doit contenir au moins 5 caractères'
            );
        }

        // Règle 2: Description minimum 10 caractères
        if (empty($evenement->getDescription())) {
            throw new \InvalidArgumentException('La description est obligatoire');
        }
        
        if (strlen($evenement->getDescription()) < 10) {
            throw new \InvalidArgumentException(
                'La description doit contenir au moins 10 caractères'
            );
        }

        // Règle 3: Lieu non vide
        if (empty($evenement->getLieu())) {
            throw new \InvalidArgumentException('Le lieu est obligatoire');
        }

        // Règle 4: Date de fin après date de début
        if ($evenement->getDateFin() <= $evenement->getDateDebut()) {
            throw new \InvalidArgumentException(
                'La date de fin doit être postérieure à la date de début'
            );
        }

        // Règle 5: Nombre max d'équipes entre 1 et 100
        if ($evenement->getNbMax() < 1 || $evenement->getNbMax() > 100) {
            throw new \InvalidArgumentException(
                'Le nombre maximum d\'équipes doit être entre 1 et 100'
            );
        }

        // Règle 6: Date de début dans le futur
        $now = new \DateTime();
        if ($evenement->getDateDebut() <= $now) {
            throw new \InvalidArgumentException(
                'La date de début doit être dans le futur'
            );
        }

        return true;
    }
}
