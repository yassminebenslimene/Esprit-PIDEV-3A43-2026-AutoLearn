<?php
// Déclaration du fichier PHP

// Définition du namespace pour le contrôleur du tuteur IA
namespace App\Controller\FrontOffice;

// Import de l'entité Quiz
use App\Entity\Quiz;
// Import de l'entité Etudiant
use App\Entity\Etudiant;
// Import du service de tuteur IA
use App\Service\QuizTutorAIService;
// Import du contrôleur de base Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Import de la classe Request pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Request;
// Import de JsonResponse pour les réponses JSON
use Symfony\Component\HttpFoundation\JsonResponse;
// Import de l'interface Session pour gérer les sessions
use Symfony\Component\HttpFoundation\Session\SessionInterface;
// Import de l'attribut Route pour définir les routes
use Symfony\Component\Routing\Annotation\Route;
// Import de l'attribut IsGranted pour restreindre l'accès
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Préfixe de route pour toutes les méthodes de ce contrôleur
#[Route('/quiz/tutor')]
// Restreint l'accès aux étudiants uniquement
#[IsGranted('ROLE_ETUDIANT')]
// Classe contrôleur pour gérer le tuteur IA des quiz
class QuizTutorController extends AbstractController
{
    // Constructeur avec injection du service de tuteur IA
    public function __construct(
        // Service qui gère les interactions avec l'IA tuteur
        private QuizTutorAIService $tutorService
    ) {}

    /**
     * Endpoint pour poser une question au tuteur IA
     */
    // Route API pour poser une question au tuteur (POST uniquement, retourne JSON)
    #[Route('/{id}/ask', name: 'app_quiz_tutor_ask', methods: ['POST'])]
    // Méthode pour traiter une question de l'étudiant
    public function ask(
        Quiz $quiz,
        Request $request,
        SessionInterface $session
    ): JsonResponse {
        // Récupère l'étudiant connecté
        $etudiant = $this->getUser();
        
        // Vérifie que l'utilisateur est un étudiant
        if (!$etudiant instanceof Etudiant) {
            // Retourne une erreur JSON avec code HTTP 403
            return new JsonResponse([
                'success' => false,
                'error' => 'Non autorisé'
            ], 403);
        }

        // Décode le contenu JSON de la requête
        $data = json_decode($request->getContent(), true);
        // Récupère la question de l'étudiant (ou chaîne vide si absente)
        $questionEtudiant = $data['question'] ?? '';
        
        // Vérifie que la question n'est pas vide
        if (empty(trim($questionEtudiant))) {
            // Retourne une erreur JSON avec code HTTP 400 (Bad Request)
            return new JsonResponse([
                'success' => false,
                'error' => 'La question ne peut pas être vide'
            ], 400);
        }

        // Crée la clé de session pour récupérer les résultats du quiz
        $resultKey = 'quiz_result_' . $quiz->getId() . '_' . $etudiant->getId();
        
        // Vérifie que les résultats existent en session
        if (!$session->has($resultKey)) {
            // Retourne une erreur JSON avec code HTTP 404
            return new JsonResponse([
                'success' => false,
                'error' => 'Résultats du quiz non trouvés. Veuillez repasser le quiz.'
            ], 404);
        }

        // Récupère les données des résultats depuis la session
        $resultData = $session->get($resultKey);
        // Extrait les détails des réponses (correctes/incorrectes)
        $resultDetails = $resultData['details'] ?? [];

        // Crée la clé de session pour l'historique de conversation
        $conversationKey = 'quiz_conversation_' . $quiz->getId() . '_' . $etudiant->getId();
        // Récupère l'historique de conversation (tableau vide si inexistant)
        $conversationHistory = $session->get($conversationKey, []);

        // Limite l'historique aux 10 derniers échanges pour éviter de surcharger l'API
        $conversationHistory = array_slice($conversationHistory, -10);

        // Obtient la réponse du tuteur IA
        $response = $this->tutorService->repondreQuestion(
            $questionEtudiant,
            $quiz,
            $resultDetails,
            $conversationHistory
        );

        // Sauvegarde l'échange dans l'historique si la réponse est réussie
        if ($response['success']) {
            // Ajoute le nouvel échange à l'historique
            $conversationHistory[] = [
                // Question posée par l'étudiant
                'question' => $questionEtudiant,
                // Réponse générée par l'IA
                'reponse' => $response['reponse'],
                // Timestamp de l'échange
                'timestamp' => $response['timestamp']
            ];
            // Sauvegarde l'historique mis à jour en session
            $session->set($conversationKey, $conversationHistory);
        }

        // Retourne la réponse au format JSON
        return new JsonResponse($response);
    }

    /**
     * Endpoint pour obtenir des suggestions de questions
     */
    // Route API pour obtenir des suggestions de questions (GET uniquement)
    #[Route('/{id}/suggestions', name: 'app_quiz_tutor_suggestions', methods: ['GET'])]
    // Méthode pour générer des suggestions de questions à poser au tuteur
    public function suggestions(
        Quiz $quiz,
        SessionInterface $session
    ): JsonResponse {
        // Récupère l'étudiant connecté
        $etudiant = $this->getUser();
        
        // Vérifie que l'utilisateur est un étudiant
        if (!$etudiant instanceof Etudiant) {
            // Retourne une erreur JSON avec code HTTP 403
            return new JsonResponse([
                'success' => false,
                'error' => 'Non autorisé'
            ], 403);
        }

        // Crée la clé de session pour récupérer les résultats
        $resultKey = 'quiz_result_' . $quiz->getId() . '_' . $etudiant->getId();
        
        // Vérifie que les résultats existent en session
        if (!$session->has($resultKey)) {
            // Retourne une erreur JSON avec code HTTP 404
            return new JsonResponse([
                'success' => false,
                'error' => 'Résultats du quiz non trouvés'
            ], 404);
        }

        // Récupère les données des résultats
        $resultData = $session->get($resultKey);
        // Extrait les détails des réponses
        $resultDetails = $resultData['details'] ?? [];

        // Génère des suggestions de questions basées sur les erreurs de l'étudiant
        $suggestions = $this->tutorService->genererSuggestionsQuestions($quiz, $resultDetails);

        // Retourne les suggestions au format JSON
        return new JsonResponse([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Endpoint pour réinitialiser la conversation
     */
    // Route API pour réinitialiser l'historique de conversation (POST uniquement)
    #[Route('/{id}/reset', name: 'app_quiz_tutor_reset', methods: ['POST'])]
    // Méthode pour effacer l'historique de conversation avec le tuteur
    public function reset(
        Quiz $quiz,
        SessionInterface $session
    ): JsonResponse {
        // Récupère l'étudiant connecté
        $etudiant = $this->getUser();
        
        // Vérifie que l'utilisateur est un étudiant
        if (!$etudiant instanceof Etudiant) {
            // Retourne une erreur JSON avec code HTTP 403
            return new JsonResponse([
                'success' => false,
                'error' => 'Non autorisé'
            ], 403);
        }

        // Crée la clé de session pour l'historique de conversation
        $conversationKey = 'quiz_conversation_' . $quiz->getId() . '_' . $etudiant->getId();
        // Supprime l'historique de conversation de la session
        $session->remove($conversationKey);

        // Retourne une confirmation de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'Conversation réinitialisée'
        ]);
    }
}
