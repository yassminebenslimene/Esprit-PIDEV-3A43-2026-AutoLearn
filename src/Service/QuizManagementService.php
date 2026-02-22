<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Option;
use App\Entity\Etudiant;

/**
 * Service pour gérer la logique métier des quiz
 * Implémente les règles sans modifier la structure de la base de données
 */
class QuizManagementService
{
    /**
     * Règle 1: Vérifie si un quiz peut être activé
     */
    public function canActivateQuiz(Quiz $quiz): array
    {
        $errors = [];

        if ($quiz->getQuestions()->count() === 0) {
            $errors[] = "Le quiz doit contenir au moins une question.";
        }

        foreach ($quiz->getQuestions() as $question) {
            if ($question->getOptions()->count() < 2) {
                $errors[] = "La question '{$question->getTexteQuestion()}' doit avoir au moins 2 options.";
            }

            $hasCorrectAnswer = false;
            foreach ($question->getOptions() as $option) {
                if ($option->isEstCorrecte()) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if (!$hasCorrectAnswer) {
                $errors[] = "La question '{$question->getTexteQuestion()}' doit avoir au moins une réponse correcte.";
            }
        }

        return [
            'canActivate' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Règle 3: Vérifie si une option peut être supprimée
     */
    public function canDeleteOption(Option $option): array
    {
        $errors = [];
        $question = $option->getQuestion();

        if (!$option->isEstCorrecte()) {
            return ['canDelete' => true, 'errors' => []];
        }

        // Compter les options correctes
        $correctOptionsCount = 0;
        foreach ($question->getOptions() as $opt) {
            if ($opt->isEstCorrecte()) {
                $correctOptionsCount++;
            }
        }

        if ($correctOptionsCount <= 1) {
            $errors[] = "Impossible de supprimer cette option car c'est la seule réponse correcte de la question.";
        }

        return [
            'canDelete' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Règle 4: Vérifie si un étudiant peut passer un quiz
     */
    public function canStudentTakeQuiz(Etudiant $etudiant, Quiz $quiz): array
    {
        $errors = [];

        // Vérifier si le quiz est actif
        if ($quiz->getEtat() !== 'actif') {
            $errors[] = "Ce quiz n'est pas actif.";
        }

        return [
            'canTake' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Règle 5: Calcule le score d'un quiz
     * @param array $reponses Format: ['questionId' => 'optionId']
     */
    public function calculateScore(Quiz $quiz, array $reponses): array
    {
        $score = 0;
        $totalPoints = 0;
        $details = [];

        foreach ($quiz->getQuestions() as $question) {
            $totalPoints += $question->getPoint();
            $questionId = $question->getId();
            $isCorrect = false;
            $correctOptionId = null;

            // Trouver la bonne réponse
            foreach ($question->getOptions() as $option) {
                if ($option->isEstCorrecte()) {
                    $correctOptionId = $option->getId();
                    
                    // Vérifier si l'étudiant a donné la bonne réponse
                    if (isset($reponses[$questionId]) && $reponses[$questionId] == $option->getId()) {
                        $isCorrect = true;
                        $score += $question->getPoint();
                    }
                    break;
                }
            }

            $details[$questionId] = [
                'question' => $question,
                'selectedOption' => $reponses[$questionId] ?? null,
                'correctOption' => $correctOptionId,
                'isCorrect' => $isCorrect,
                'points' => $isCorrect ? $question->getPoint() : 0
            ];
        }

        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;

        return [
            'score' => $score,
            'totalPoints' => $totalPoints,
            'percentage' => round($percentage, 2),
            'details' => $details
        ];
    }

    /**
     * Règle 8: Mélange les questions d'un quiz
     */
    public function shuffleQuestions(Quiz $quiz): array
    {
        $questions = $quiz->getQuestions()->toArray();
        shuffle($questions);
        return $questions;
    }

    /**
     * Règle 8: Mélange les options d'une question
     */
    public function shuffleOptions(Question $question): array
    {
        $options = $question->getOptions()->toArray();
        shuffle($options);
        return $options;
    }

    /**
     * Règle 9: Prépare les données du quiz pour l'affichage (sans révéler les réponses)
     */
    public function prepareQuizForDisplay(Quiz $quiz, bool $randomize = false): array
    {
        $questions = $randomize ? $this->shuffleQuestions($quiz) : $quiz->getQuestions()->toArray();
        $quizData = [];

        foreach ($questions as $question) {
            $options = $randomize ? $this->shuffleOptions($question) : $question->getOptions()->toArray();
            
            $questionData = [
                'id' => $question->getId(),
                'texte' => $question->getTexteQuestion(),
                'points' => $question->getPoint(),
                'options' => []
            ];

            foreach ($options as $option) {
                // NE PAS inclure isEstCorrecte pour la sécurité
                $questionData['options'][] = [
                    'id' => $option->getId(),
                    'texte' => $option->getTexteOption()
                ];
            }

            $quizData[] = $questionData;
        }

        return $quizData;
    }

    /**
     * Règle 10: Génère des statistiques basiques
     * Note: Pour des statistiques complètes, il faudrait une table de tentatives
     */
    public function generateQuizStatistics(Quiz $quiz): array
    {
        $totalQuestions = $quiz->getQuestions()->count();
        $totalPoints = 0;

        foreach ($quiz->getQuestions() as $question) {
            $totalPoints += $question->getPoint();
        }

        return [
            'titre' => $quiz->getTitre(),
            'etat' => $quiz->getEtat(),
            'nombreQuestions' => $totalQuestions,
            'pointsTotal' => $totalPoints,
            'moyennePointsParQuestion' => $totalQuestions > 0 ? round($totalPoints / $totalQuestions, 2) : 0
        ];
    }
}
