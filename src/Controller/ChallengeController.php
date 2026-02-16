<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Repository\ChallengeRepository;
use App\Repository\ExerciceRepository;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChallengeController extends AbstractController
{
    // ⚠️ IMPORTANT: La route spécifique DOIT être avant la route générique
    #[Route('/challenge/save-answer', name: 'frontchallenge_save_answer', methods: ['POST'])]
    public function saveAnswer(Request $request, SessionInterface $session): JsonResponse
    {
        try {
            // Récupérer les données JSON de la requête
            $data = json_decode($request->getContent(), true);
            
            // Vérifier si le JSON est valide
            if (!$data) {
                return $this->json([
                    'success' => false, 
                    'error' => 'JSON invalide'
                ], 400);
            }
            
            // Récupérer les paramètres
            $itemId = $data['itemId'] ?? null;
            $answer = $data['answer'] ?? null;
            $challengeId = $data['challengeId'] ?? null;
            
            // Vérifier que tous les paramètres sont présents
            if (!$itemId) {
                return $this->json([
                    'success' => false, 
                    'error' => 'ID de l\'élément manquant'
                ], 400);
            }
            
            if (!$challengeId) {
                return $this->json([
                    'success' => false, 
                    'error' => 'ID du challenge manquant'
                ], 400);
            }
            
            // Utiliser l'ID du challenge pour la clé de session
            $sessionKey = 'challenge_answers_' . $challengeId;
            $sessionAnswers = $session->get($sessionKey, []);
            
            // Sauvegarder la réponse
            $sessionAnswers[$itemId] = [
                'answer' => $answer,
                'timestamp' => time()
            ];
            
            // Mettre à jour la session
            $session->set($sessionKey, $sessionAnswers);
            
            return $this->json([
                'success' => true, 
                'message' => 'Réponse sauvegardée avec succès'
            ]);
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner les détails pour le débogage
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Route pour afficher les détails d'un challenge
    #[Route('/challenge/{id}', name: 'frontchallenge')]
    public function showChallenge(int $id, ChallengeRepository $challengeRepository): Response
    {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
        return $this->render('frontoffice/challenge_show.html.twig', [
            'challenge' => $challenge
        ]);
    }

    // Route pour jouer à un challenge (avec progression)
    #[Route('/challenge/{id}/play/{index}', name: 'frontchallenge_play', defaults: ['index' => 0])]
    public function playChallenge(
        int $id, 
        int $index, 
        ChallengeRepository $challengeRepository,
        SessionInterface $session
    ): Response {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
        // Récupérer ou initialiser la session pour ce challenge
        $sessionKey = 'challenge_answers_' . $id;
        $sessionAnswers = $session->get($sessionKey, []);
        
        // Fusionner tous les éléments (exercices + quiz)
        $allItems = [];
        
        // Ajouter les exercices
        foreach ($challenge->getExercices() as $exercice) {
            $allItems[] = [
                'type' => 'exercice', 
                'item' => $exercice
            ];
        }
        
        // Ajouter les quiz
        foreach ($challenge->getQuizzes() as $quiz) {
            $allItems[] = [
                'type' => 'quiz', 
                'item' => $quiz
            ];
        }
        
        // Vérifier s'il y a du contenu
        if (empty($allItems)) {
            throw $this->createNotFoundException('Ce challenge ne contient aucun contenu');
        }
        
        // Valider l'index
        if ($index < 0 || $index >= count($allItems)) {
            $index = 0;
        }
        
        // Récupérer l'élément courant
        $currentItem = $allItems[$index];
        
        return $this->render('frontoffice/challenge_play.html.twig', [
            'challenge' => $challenge,
            'currentItem' => $currentItem['item'],
            'currentType' => $currentItem['type'],
            'currentIndex' => $index,
            'totalItems' => count($allItems),
            'sessionAnswers' => $sessionAnswers
        ]);
    }

    // Route pour terminer un challenge et voir le score
    #[Route('/challenge/{id}/complete', name: 'frontchallenge_complete')]
    public function completeChallenge(
        int $id, 
        ChallengeRepository $challengeRepository,
        SessionInterface $session
    ): Response {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
        // Récupérer les réponses de la session
        $sessionKey = 'challenge_answers_' . $id;
        $sessionAnswers = $session->get($sessionKey, []);
        
        // Calculer le score total et le score obtenu
        $totalPoints = 0;
        $earnedPoints = 0;
        
        // Vérifier les exercices
        foreach ($challenge->getExercices() as $exercice) {
            $totalPoints += $exercice->getPoints();
            
            if (isset($sessionAnswers[$exercice->getId()])) {
                $userAnswer = $sessionAnswers[$exercice->getId()]['answer'] ?? '';
                // Comparaison insensible à la casse et sans espaces superflus
                if (strtolower(trim($userAnswer)) === strtolower(trim($exercice->getReponse()))) {
                    $earnedPoints += $exercice->getPoints();
                }
            }
        }
        
        // Optionnel: Ajouter la logique pour les quiz si nécessaire
        
        // Nettoyer la session après completion
        $session->remove($sessionKey);
        
        return $this->render('frontoffice/challenge_complete.html.twig', [
            'challenge' => $challenge,
            'score' => $earnedPoints,
            'totalPoints' => $totalPoints
        ]);
    }
}