<?php

namespace App\Service;

use App\Entity\Quiz;

// Service métier pour gérer la logique des quiz
class QuizManager
{
    // Vérifie si un étudiant a réussi le quiz en comparant son score au seuil de réussite
    public function isQuizPassed(Quiz $quiz, float $score): bool
    {
        // Validation : Le score doit être entre 0 et 100
        if ($score < 0 || $score > 100) {
            throw new \InvalidArgumentException("Le score doit être entre 0 et 100. Score fourni : $score");
        }

        // Validation : Le seuil de réussite doit être valide
        $seuil = $quiz->getSeuilReussite();
        if ($seuil !== null && ($seuil < 0 || $seuil > 100)) {
            throw new \InvalidArgumentException("Le seuil de réussite doit être entre 0 et 100. Seuil : $seuil");
        }

        // Utiliser 50% par défaut si aucun seuil n'est défini
        $seuil = $seuil ?? 50;
        
        return $score >= $seuil;
    }

    // Vérifie si un quiz est actif (état = 'actif')
    public function isQuizActive(Quiz $quiz): bool
    {
        // Validation : L'état du quiz ne doit pas être vide
        $etat = $quiz->getEtat();
        if (empty($etat)) {
            throw new \InvalidArgumentException("L'état du quiz ne peut pas être vide");
        }

        // Validation : L'état doit être valide
        $etatsValides = ['actif', 'inactif', 'brouillon', 'archive'];
        if (!in_array($etat, $etatsValides)) {
            throw new \InvalidArgumentException("État invalide : $etat. États valides : " . implode(', ', $etatsValides));
        }

        return $etat === 'actif';
    }

    // Compte le nombre de questions dans un quiz
    public function countQuestions(Quiz $quiz): int
    {
        // Validation : Le quiz doit avoir une collection de questions
        $questions = $quiz->getQuestions();
        if ($questions === null) {
            throw new \InvalidArgumentException("Le quiz n'a pas de collection de questions initialisée");
        }

        $count = $questions->count();

        // Validation : Un quiz doit avoir au moins une question
        if ($count === 0) {
            throw new \InvalidArgumentException("Le quiz doit contenir au moins une question");
        }

        return $count;
    }
}
