<?php
// Déclaration du fichier PHP

// Définition du namespace pour le contrôleur de passage de quiz
namespace App\Controller\FrontOffice;

// Import de l'entité Quiz
use App\Entity\Quiz;
// Import de l'entité Etudiant
use App\Entity\Etudiant;
// Import du service de gestion des quiz
use App\Service\QuizManagementService;
// Import du contrôleur de base Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Import de la classe Request pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Request;
// Import de la classe Response pour les réponses HTTP
use Symfony\Component\HttpFoundation\Response;
// Import de JsonResponse pour les réponses JSON (API)
use Symfony\Component\HttpFoundation\JsonResponse;
// Import de l'interface Session pour gérer les sessions
use Symfony\Component\HttpFoundation\Session\SessionInterface;
// Import de l'attribut Route pour définir les routes
use Symfony\Component\Routing\Annotation\Route;
// Import de l'attribut IsGranted pour restreindre l'accès
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Préfixe de route pour toutes les méthodes de ce contrôleur
#[Route('/quiz')]
// Restreint l'accès à tous les étudiants uniquement
#[IsGranted('ROLE_ETUDIANT')]
// Classe contrôleur pour gérer le passage des quiz par les étudiants
class QuizPassageController extends AbstractController
{
    // Constructeur avec injection de dépendances
    public function __construct(
        // Service de gestion métier des quiz
        private QuizManagementService $quizService,
        // Service de correction automatique par IA
        private \App\Service\QuizCorrectorAIService $correctorAI
    ) {}

    // Route pour démarrer un quiz
    #[Route('/{id}/start', name: 'app_quiz_start')]
    // Méthode pour initialiser et démarrer un quiz
    public function start(Quiz $quiz, SessionInterface $session): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $etudiant = $this->getUser();
        
        // Vérifie que l'utilisateur est bien un étudiant
        if (!$etudiant instanceof Etudiant) {
            // Affiche un message d'erreur si ce n'est pas un étudiant
            $this->addFlash('error', 'Seuls les étudiants peuvent passer les quiz.');
            // Redirige vers la page d'accueil du front-office
            return $this->redirectToRoute('app_frontoffice');
        }

        // Vérifie si l'étudiant peut passer le quiz (état actif, tentatives restantes, etc.)
        $check = $this->quizService->canStudentTakeQuiz($etudiant, $quiz, $session);
        // Si l'étudiant ne peut pas passer le quiz
        if (!$check['canTake']) {
            // Affiche chaque message d'erreur
            foreach ($check['errors'] as $error) {
                $this->addFlash('error', $error);
            }
            // Redirige vers la liste des quiz du chapitre
            return $this->redirectToRoute('app_frontoffice_quiz_list', [
                'chapitreId' => $quiz->getChapitre()?->getId()
            ]);
        }

        // Crée une clé unique pour identifier cette tentative en session
        $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
        // Vérifie s'il y a déjà une tentative en cours
        if ($session->has($tentativeKey)) {
            // Affiche un avertissement
            $this->addFlash('warning', 'Vous avez déjà une tentative en cours pour ce quiz.');
        }

        // Prépare les données du quiz (questions et options mélangées)
        $quizData = $this->quizService->prepareQuizForDisplay($quiz, true);

        // Crée un objet DateTime pour enregistrer l'heure de début
        $dateDebut = new \DateTime();
        // Stocke les informations de la tentative en session
        $session->set($tentativeKey, [
            // ID de l'étudiant
            'etudiant_id' => $etudiant->getId(),
            // ID du quiz
            'quiz_id' => $quiz->getId(),
            // Date de début au format texte
            'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
            // Timestamp Unix pour les calculs de durée
            'timestamp_debut' => $dateDebut->getTimestamp(),
            // Données du quiz (questions et options)
            'quiz_data' => $quizData
        ]);

        // Récupère le chapitre parent du quiz
        $chapitre = $quiz->getChapitre();
        // Initialise les variables pour la navigation
        $previousQuiz = null;
        $nextQuiz = null;
        
        // Si le quiz appartient à un chapitre
        if ($chapitre) {
            // Récupère tous les quiz du chapitre sous forme de tableau
            $allQuizzes = $chapitre->getQuizzes()->toArray();
            // Filtre uniquement les quiz actifs (fonction anonyme)
            $allQuizzes = array_filter($allQuizzes, fn($q) => $q->getEtat() === 'actif');
            // Réindexe le tableau pour avoir des clés numériques continues
            $allQuizzes = array_values($allQuizzes);
            
            // Trouve l'index du quiz actuel dans le tableau
            $currentIndex = array_search($quiz, $allQuizzes, true);
            
            // Si le quiz a été trouvé dans le tableau
            if ($currentIndex !== false) {
                // Si ce n'est pas le premier quiz
                if ($currentIndex > 0) {
                    // Récupère le quiz précédent
                    $previousQuiz = $allQuizzes[$currentIndex - 1];
                }
                // Si ce n'est pas le dernier quiz
                if ($currentIndex < count($allQuizzes) - 1) {
                    // Récupère le quiz suivant
                    $nextQuiz = $allQuizzes[$currentIndex + 1];
                }
            }
        }

        // Retourne la vue de passage du quiz
        return $this->render('frontoffice/quiz/passage.html.twig', [
            // Objet quiz complet
            'quiz' => $quiz,
            // Données formatées du quiz (questions et options)
            'quizData' => $quizData,
            // Chapitre parent
            'chapitre' => $chapitre,
            // Durée maximale en minutes (null si illimité)
            'dureeMaxMinutes' => $quiz->getDureeMaxMinutes(),
            // Timestamp de début pour le timer JavaScript
            'timestampDebut' => $dateDebut->getTimestamp(),
            // Quiz précédent pour la navigation
            'previousQuiz' => $previousQuiz,
            // Quiz suivant pour la navigation
            'nextQuiz' => $nextQuiz
        ]);
    }

    // Route pour soumettre les réponses d'un quiz (POST uniquement)
    #[Route('/{id}/submit', name: 'app_quiz_submit', methods: ['POST'])]
    // Méthode pour traiter la soumission des réponses d'un quiz
    public function submit(
        Quiz $quiz, 
        Request $request, 
        SessionInterface $session
    ): Response {
        // Récupère l'étudiant connecté
        $etudiant = $this->getUser();
        
        // Vérifie que l'utilisateur est bien un étudiant
        if (!$etudiant instanceof Etudiant) {
            // Lance une exception d'accès refusé si ce n'est pas un étudiant
            throw $this->createAccessDeniedException();
        }

        // Crée la clé de session pour cette tentative
        $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
        
        // Vérifie qu'il y a bien une tentative en cours
        if (!$session->has($tentativeKey)) {
            // Affiche une erreur si aucune tentative n'est en cours
            $this->addFlash('error', 'Aucune tentative en cours pour ce quiz.');
            // Redirige vers la liste des quiz
            return $this->redirectToRoute('app_frontoffice_quiz_list', [
                'chapitreId' => $quiz->getChapitre()?->getId()
            ]);
        }

        // Récupère les données de la tentative depuis la session
        $tentative = $session->get($tentativeKey);
        // Crée un objet DateTime pour l'heure de fin
        $dateFin = new \DateTime();
        
        // Reconstitue la date de début depuis la session
        $dateDebut = \DateTime::createFromFormat('Y-m-d H:i:s', $tentative['date_debut']);
        // Calcule la durée réelle en secondes
        $dureeReelleSecondes = $dateFin->getTimestamp() - $dateDebut->getTimestamp();
        // Convertit la durée en minutes avec 2 décimales
        $dureeReelleMinutes = round($dureeReelleSecondes / 60, 2);
        
        // Initialise le flag de dépassement de temps
        $tempsDepasse = false;
        // Vérifie si le quiz a une durée maximale définie
        if ($quiz->getDureeMaxMinutes()) {
            // Convertit la durée max en secondes
            $dureeMaxSecondes = $quiz->getDureeMaxMinutes() * 60;
            // Vérifie si le temps a été dépassé
            if ($dureeReelleSecondes > $dureeMaxSecondes) {
                // Active le flag de dépassement
                $tempsDepasse = true;
                // Affiche un avertissement
                $this->addFlash('warning', 'Le temps maximum a été dépassé. Soumission automatique effectuée.');
            }
        }
        
        // Récupère toutes les réponses soumises (format: ['questionId' => 'optionId'])
        $reponses = $request->request->all('answers');
        
        // Calcule le score en comparant les réponses avec les bonnes réponses
        $result = $this->quizService->calculateScore($quiz, $reponses);
        
        // Enregistre la tentative en session (incrémente le compteur)
        $this->quizService->enregistrerTentative($etudiant, $quiz, $session, $result);
        
        // Récupère le seuil de réussite du quiz (défaut: 50%)
        $seuilReussite = $quiz->getSeuilReussite() ?? 50;
        // Détermine si le quiz est validé ou échoué
        $statut = $result['percentage'] >= $seuilReussite ? 'VALIDÉ' : 'ÉCHEC';
        
        // Supprime la tentative en cours de la session
        $session->remove($tentativeKey);
        
        // Crée une clé pour stocker les résultats (pour le tuteur IA)
        $resultKey = 'quiz_result_' . $quiz->getId() . '_' . $etudiant->getId();
        // Sauvegarde les résultats détaillés en session
        $session->set($resultKey, [
            // Détails de chaque question (correcte/incorrecte)
            'details' => $result['details'],
            // Pourcentage de réussite
            'percentage' => $result['percentage'],
            // Score obtenu
            'score' => $result['score'],
            // Timestamp de la soumission
            'timestamp' => time()
        ]);
        
        // Obtient les statistiques globales de l'étudiant pour ce quiz
        $statistiques = $this->quizService->getStatistiquesEtudiant($etudiant, $quiz, $session);
        
        // Initialise les tableaux pour les explications IA
        $explications = [];
        $resumePedagogique = [];
        // Bloc try-catch pour gérer les erreurs de l'IA
        try {
            // Génère des explications personnalisées pour chaque question
            $explications = $this->correctorAI->genererExplicationsPersonnalisees($result['details']);
            // Génère un résumé pédagogique global
            $resumePedagogique = $this->correctorAI->genererResumePedagogique($result['details'], $result['percentage']);
        } catch (\Exception $e) {
            // En cas d'erreur, continue sans les explications IA
            $this->addFlash('warning', 'Les explications IA ne sont pas disponibles pour le moment.');
        }
        
        // Retourne la vue des résultats avec les explications IA
        return $this->render('frontoffice/quiz/result_with_ai.html.twig', [
            // Objet quiz complet
            'quiz' => $quiz,
            // Résultats détaillés (score, pourcentage, détails par question)
            'result' => $result,
            // Statut final (VALIDÉ ou ÉCHEC)
            'statut' => $statut,
            // Seuil de réussite du quiz
            'seuilReussite' => $seuilReussite,
            // Chapitre parent
            'chapitre' => $quiz->getChapitre(),
            // Durée réelle de passage en minutes
            'dureeReelle' => $dureeReelleMinutes,
            // Indique si le temps a été dépassé
            'tempsDepasse' => $tempsDepasse,
            // Statistiques de l'étudiant (nombre de tentatives, etc.)
            'statistiques' => $statistiques,
            // Explications IA pour chaque question
            'explications' => $explications,
            // Résumé pédagogique global généré par l'IA
            'resumePedagogique' => $resumePedagogique
        ]);
    }

    // Route API pour vérifier le temps restant (GET uniquement, retourne du JSON)
    #[Route('/{id}/check-time', name: 'app_quiz_check_time', methods: ['GET'])]
    // Méthode pour vérifier le temps écoulé et restant (appelée par JavaScript)
    public function checkTime(Quiz $quiz, SessionInterface $session): JsonResponse
    {
        // Récupère l'étudiant connecté
        $etudiant = $this->getUser();
        
        // Vérifie que l'utilisateur est un étudiant
        if (!$etudiant instanceof Etudiant) {
            // Retourne une erreur JSON avec code HTTP 403 (Forbidden)
            return new JsonResponse(['error' => 'Non autorisé'], 403);
        }

        // Crée la clé de session pour cette tentative
        $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
        
        // Vérifie qu'il y a une tentative en cours
        if (!$session->has($tentativeKey)) {
            // Retourne une erreur JSON avec code HTTP 404 (Not Found)
            return new JsonResponse(['error' => 'Aucune tentative en cours'], 404);
        }

        // Récupère les données de la tentative
        $tentative = $session->get($tentativeKey);
        // Récupère le timestamp actuel
        $now = time();
        // Calcule le temps écoulé en secondes
        $tempsEcoule = $now - $tentative['timestamp_debut'];
        
        // Prépare le tableau de données à retourner
        $data = [
            // Temps écoulé depuis le début en secondes
            'temps_ecoule_secondes' => $tempsEcoule,
            // Durée maximale autorisée en secondes (null si illimité)
            'duree_max_secondes' => $quiz->getDureeMaxMinutes() ? $quiz->getDureeMaxMinutes() * 60 : null,
            // Temps restant en secondes (calculé ci-dessous)
            'temps_restant_secondes' => null,
            // Indique si le temps est expiré
            'temps_expire' => false
        ];
        
        // Si le quiz a une durée maximale définie
        if ($quiz->getDureeMaxMinutes()) {
            // Convertit la durée max en secondes
            $dureeMaxSecondes = $quiz->getDureeMaxMinutes() * 60;
            // Calcule le temps restant (minimum 0)
            $data['temps_restant_secondes'] = max(0, $dureeMaxSecondes - $tempsEcoule);
            // Vérifie si le temps est expiré
            $data['temps_expire'] = $tempsEcoule >= $dureeMaxSecondes;
        }
        
        // Retourne les données au format JSON
        return new JsonResponse($data);
    }
}
