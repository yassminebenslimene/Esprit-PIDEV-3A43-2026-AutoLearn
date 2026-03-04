<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\Option;
use App\Entity\Etudiant;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Service pour gérer la logique métier des quiz
 * Implémente les règles sans modifier la structure de la base de données
 */
class QuizManagementService
{
    /**
     * Clé de session pour stocker les tentatives des étudiants
     */
    private const SESSION_TENTATIVES_KEY = 'quiz_tentatives';

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
    public function canStudentTakeQuiz(Etudiant $etudiant, Quiz $quiz, SessionInterface $session = null): array
    {
        $errors = [];

        // Vérifier si le quiz est actif
        if ($quiz->getEtat() !== 'actif') {
            $errors[] = "Ce quiz n'est pas actif.";
        }

        // Vérifier s'il y a une tentative en cours
        if ($session) {
            $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
            if ($session->has($tentativeKey)) {
                $errors[] = "Vous avez déjà une tentative en cours pour ce quiz.";
            }

            // Vérifier le nombre maximum de tentatives
            if ($quiz->getMaxTentatives() !== null) {
                $nombreTentatives = $this->getNombreTentatives($etudiant, $quiz, $session);
                if ($nombreTentatives >= $quiz->getMaxTentatives()) {
                    $errors[] = "Vous avez atteint le nombre maximum de tentatives ({$quiz->getMaxTentatives()}) pour ce quiz.";
                }
            }
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
                'imageName' => $question->getImageName(), // Nom de l'image
                'question' => $question, // Objet question complet pour VichUploader
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

    /**
     * Obtient le nombre de tentatives d'un étudiant pour un quiz
     */
    public function getNombreTentatives(Etudiant $etudiant, Quiz $quiz, SessionInterface $session): int
    {
        $tentativesKey = self::SESSION_TENTATIVES_KEY;
        $tentatives = $session->get($tentativesKey, []);
        
        $key = $etudiant->getId() . '_' . $quiz->getId();
        return $tentatives[$key] ?? 0;
    }

    /**
     * Enregistre une nouvelle tentative
     */
    public function enregistrerTentative(Etudiant $etudiant, Quiz $quiz, SessionInterface $session, array $resultats = null): void
    {
        $tentativesKey = self::SESSION_TENTATIVES_KEY;
        $tentatives = $session->get($tentativesKey, []);
        
        $key = $etudiant->getId() . '_' . $quiz->getId();
        $tentatives[$key] = ($tentatives[$key] ?? 0) + 1;
        
        // Optionnel : stocker aussi les résultats de la dernière tentative
        if ($resultats) {
            $resultatsKey = 'quiz_resultats_' . $etudiant->getId() . '_' . $quiz->getId();
            $session->set($resultatsKey, [
                'score' => $resultats['score'],
                'totalPoints' => $resultats['totalPoints'],
                'percentage' => $resultats['percentage'],
                'date' => (new \DateTime())->format('Y-m-d H:i:s'),
                'tentative' => $tentatives[$key]
            ]);
        }
        
        $session->set($tentativesKey, $tentatives);
    }

    /**
     * Obtient les résultats de la dernière tentative
     */
    public function getDerniersResultats(Etudiant $etudiant, Quiz $quiz, SessionInterface $session): ?array
    {
        $resultatsKey = 'quiz_resultats_' . $etudiant->getId() . '_' . $quiz->getId();
        return $session->get($resultatsKey);
    }

    /**
     * Vérifie si l'étudiant a réussi le quiz (basé sur la dernière tentative)
     */
    public function aReussiQuiz(Etudiant $etudiant, Quiz $quiz, SessionInterface $session): bool
    {
        $resultats = $this->getDerniersResultats($etudiant, $quiz, $session);
        
        if (!$resultats) {
            return false;
        }
        
        return $resultats['percentage'] >= $quiz->getSeuilReussite();
    }

    /**
     * Obtient les statistiques d'un étudiant pour un quiz
     */
    public function getStatistiquesEtudiant(Etudiant $etudiant, Quiz $quiz, SessionInterface $session): array
    {
        $nombreTentatives = $this->getNombreTentatives($etudiant, $quiz, $session);
        $derniersResultats = $this->getDerniersResultats($etudiant, $quiz, $session);
        $aReussi = $this->aReussiQuiz($etudiant, $quiz, $session);
        $peutRecommencer = $this->canStudentTakeQuiz($etudiant, $quiz, $session)['canTake'];

        return [
            'nombreTentatives' => $nombreTentatives,
            'maxTentatives' => $quiz->getMaxTentatives(),
            'derniersResultats' => $derniersResultats,
            'aReussi' => $aReussi,
            'peutRecommencer' => $peutRecommencer,
            'seuilReussite' => $quiz->getSeuilReussite()
        ];
    }

    /**
     * Valide qu'un quiz respecte les règles métier obligatoires
     * 
     * @param Quiz $quiz Le quiz à valider
     * @return array Tableau avec 'valid' (bool) et 'errors' (array)
     */
    public function validateQuizBusinessRules(Quiz $quiz): array
    {
        $errors = [];
        
        // Règle obligatoire : Un quiz doit appartenir à un chapitre
        if ($quiz->getChapitre() === null) {
            $errors[] = '🔒 Un quiz doit obligatoirement appartenir à un chapitre.';
        }
        
        // Validation du titre
        if (empty($quiz->getTitre()) || strlen(trim($quiz->getTitre())) < 3) {
            $errors[] = 'Le titre du quiz doit contenir au moins 3 caractères.';
        }
        
        // Validation de l'état
        $etatsValides = ['actif', 'inactif', 'brouillon', 'archive'];
        if (!in_array($quiz->getEtat(), $etatsValides)) {
            $errors[] = 'L\'état du quiz doit être: actif, inactif, brouillon ou archive.';
        }
        
        // Validation des tentatives
        if ($quiz->getMaxTentatives() !== null && $quiz->getMaxTentatives() <= 0) {
            $errors[] = 'Le nombre maximum de tentatives doit être positif.';
        }
        
        // Validation du seuil de réussite
        if ($quiz->getSeuilReussite() !== null && ($quiz->getSeuilReussite() < 0 || $quiz->getSeuilReussite() > 100)) {
            $errors[] = 'Le seuil de réussite doit être entre 0% et 100%.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}