<?php

namespace App\Controller;

use App\Service\AIAssistantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[Route('/ai-assistant')]
class AIAssistantController extends AbstractController
{
    private AIAssistantService $aiAssistant;

    public function __construct(AIAssistantService $aiAssistant)
    {
        $this->aiAssistant = $aiAssistant;
    }

    /**
     * Point d'entrée principal - Pose une question à l'IA
     */
    #[Route('/ask', name: 'ai_assistant_ask', methods: ['POST'])]
    public function ask(Request $request): JsonResponse
    {
        // Vérifier l'authentification
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'error' => 'Authentification requise'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'success' => false,
                'error' => 'JSON invalide'
            ], Response::HTTP_BAD_REQUEST);
        }

        $question = $data['question'] ?? '';

        if (empty(trim($question))) {
            return $this->json([
                'success' => false,
                'error' => 'Question vide'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Limiter la longueur de la question
        if (strlen($question) > 500) {
            return $this->json([
                'success' => false,
                'error' => 'Question trop longue (max 500 caractères)'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Options optionnelles
        $options = [
            'temperature' => $data['temperature'] ?? 0.7,
            'model' => $data['model'] ?? null
        ];

        try {
            // Générer la réponse
            $result = $this->aiAssistant->ask($question, $options);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtenir des suggestions de questions
     */
    #[Route('/suggestions', name: 'ai_assistant_suggestions', methods: ['GET'])]
    public function suggestions(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'error' => 'Authentification requise'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $user = $this->getUser();
        $role = $user ? $user->getRole() : 'ETUDIANT';
        $language = $request->query->get('language', 'fr');

        try {
            $suggestions = $this->aiAssistant->getSuggestions($role, $language);

            return $this->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur serveur'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Vérifier le statut du service IA
     */
    #[Route('/status', name: 'ai_assistant_status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        // Accessible uniquement aux admins
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $status = $this->aiAssistant->getStatus();

        return $this->json($status);
    }

    /**
     * Page de test de l'assistant (dev uniquement)
     */
    #[Route('/test', name: 'ai_assistant_test', methods: ['GET'])]
    public function test(): Response
    {
        // Accessible uniquement en mode dev
        if ($this->getParameter('kernel.environment') !== 'dev') {
            throw $this->createNotFoundException();
        }

        return $this->render('ai_assistant/test.html.twig', [
            'status' => $this->aiAssistant->getStatus()
        ]);
    }
}
