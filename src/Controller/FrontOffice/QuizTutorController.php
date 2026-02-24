<?php

namespace App\Controller\FrontOffice;

use App\Entity\Quiz;
use App\Entity\Etudiant;
use App\Service\QuizTutorAIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/quiz/tutor')]
#[IsGranted('ROLE_ETUDIANT')]
class QuizTutorController extends AbstractController
{
    public function __construct(
        private QuizTutorAIService $tutorService
    ) {}

    /**
     * Endpoint pour poser une question au tuteur IA
     */
    #[Route('/{id}/ask', name: 'app_quiz_tutor_ask', methods: ['POST'])]
    public function ask(
        Quiz $quiz,
        Request $request,
        SessionInterface $session
    ): JsonResponse {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Non autorisé'
            ], 403);
        }

        // Récupérer la question de l'étudiant
        $data = json_decode($request->getContent(), true);
        $questionEtudiant = $data['question'] ?? '';
        
        if (empty(trim($questionEtudiant))) {
            return new JsonResponse([
                'success' => false,
                'error' => 'La question ne peut pas être vide'
            ], 400);
        }

        // Récupérer les résultats du quiz depuis la session
        $resultKey = 'quiz_result_' . $quiz->getId() . '_' . $etudiant->getId();
        
        if (!$session->has($resultKey)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Résultats du quiz non trouvés. Veuillez repasser le quiz.'
            ], 404);
        }

        $resultData = $session->get($resultKey);
        $resultDetails = $resultData['details'] ?? [];

        // Récupérer l'historique de conversation
        $conversationKey = 'quiz_conversation_' . $quiz->getId() . '_' . $etudiant->getId();
        $conversationHistory = $session->get($conversationKey, []);

        // Limiter l'historique aux 10 derniers échanges pour éviter de surcharger l'API
        $conversationHistory = array_slice($conversationHistory, -10);

        // Obtenir la réponse du tuteur IA
        $response = $this->tutorService->repondreQuestion(
            $questionEtudiant,
            $quiz,
            $resultDetails,
            $conversationHistory
        );

        // Sauvegarder l'échange dans l'historique
        if ($response['success']) {
            $conversationHistory[] = [
                'question' => $questionEtudiant,
                'reponse' => $response['reponse'],
                'timestamp' => $response['timestamp']
            ];
            $session->set($conversationKey, $conversationHistory);
        }

        return new JsonResponse($response);
    }

    /**
     * Endpoint pour obtenir des suggestions de questions
     */
    #[Route('/{id}/suggestions', name: 'app_quiz_tutor_suggestions', methods: ['GET'])]
    public function suggestions(
        Quiz $quiz,
        SessionInterface $session
    ): JsonResponse {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Non autorisé'
            ], 403);
        }

        // Récupérer les résultats du quiz
        $resultKey = 'quiz_result_' . $quiz->getId() . '_' . $etudiant->getId();
        
        if (!$session->has($resultKey)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Résultats du quiz non trouvés'
            ], 404);
        }

        $resultData = $session->get($resultKey);
        $resultDetails = $resultData['details'] ?? [];

        $suggestions = $this->tutorService->genererSuggestionsQuestions($quiz, $resultDetails);

        return new JsonResponse([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Endpoint pour réinitialiser la conversation
     */
    #[Route('/{id}/reset', name: 'app_quiz_tutor_reset', methods: ['POST'])]
    public function reset(
        Quiz $quiz,
        SessionInterface $session
    ): JsonResponse {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Non autorisé'
            ], 403);
        }

        $conversationKey = 'quiz_conversation_' . $quiz->getId() . '_' . $etudiant->getId();
        $session->remove($conversationKey);

        return new JsonResponse([
            'success' => true,
            'message' => 'Conversation réinitialisée'
        ]);
    }
}
