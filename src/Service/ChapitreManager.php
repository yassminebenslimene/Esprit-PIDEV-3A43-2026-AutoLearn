<?php

namespace App\Service;

use App\Entity\GestionDeCours\Chapitre;
use InvalidArgumentException;

class ChapitreManager
{
    /**
     * Valide un chapitre selon les règles métier
     * 
     * @param Chapitre $chapitre
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validate(Chapitre $chapitre): bool
    {
        // Règle 1: Le titre est obligatoire
        if (empty($chapitre->getTitre())) {
            throw new InvalidArgumentException('Le titre du chapitre est obligatoire');
        }

        // Règle 2: Le contenu ne doit pas être vide
        if (empty($chapitre->getContenu())) {
            throw new InvalidArgumentException('Le contenu du chapitre ne peut pas être vide');
        }

        // Règle 3: L'ordre doit être un nombre positif
        if ($chapitre->getOrdre() === null || $chapitre->getOrdre() <= 0) {
            throw new InvalidArgumentException('L\'ordre du chapitre doit être un nombre positif');
        }

        return true;
    }
}
