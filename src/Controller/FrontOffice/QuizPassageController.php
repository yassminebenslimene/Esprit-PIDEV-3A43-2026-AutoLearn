<?php

namespace App\Controller\FrontOffice;

use App\Entity\Quiz;
use App\Entity\Etudiant;
use App\Service\QuizManagementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/quiz')]
#[IsGranted('ROLE_ETUDIANT')]
class QuizPassageController extends AbstractController
{
    public function __construct(
        private QuizManagementService $quizService,
        private \App\Service\QuizCorrectorAIService $correctorAI
    ) {}

    #[Route('/{id}/start', name: 'app_quiz_start')]
    public function start(Quiz $quiz, SessionInterface $session): Response
    {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            $this->addFlash('error', 'Seuls les étudiants peuvent passer les quiz.');
            return $this->redirectToRoute('app_frontoffice');
        }

        // Vérifier si l'étudiant peut passer le quiz (avec vérification des tentatives)
        $check = $this->quizService->canStudentTakeQuiz($etudiant, $quiz, $session);
        if (!$check['canTake']) {
            foreach ($check['errors'] as $error) {
                $this->addFlash('error', $error);
            }
            return $this->redirectToRoute('app_frontoffice_quiz_list', [
                'chapitreId' => $quiz->getChapitre()?->getId()
            ]);
        }

        // Vérifier s'il y a déjà une tentative en cours
        $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
        if ($session->has($tentativeKey)) {
            $this->addFlash('warning', 'Vous avez déjà une tentative en cours pour ce quiz.');
        }

        // Préparer le quiz (avec randomisation)
        $quizData = $this->quizService->prepareQuizForDisplay($quiz, true);

        // Stocker le début de la tentative avec timestamp
        $dateDebut = new \DateTime();
        $session->set($tentativeKey, [
            'etudiant_id' => $etudiant->getId(),
            'quiz_id' => $quiz->getId(),
            'date_debut' => $dateDebut->format('Y-m-d H:i:s'),
            'timestamp_debut' => $dateDebut->getTimestamp(),
            'quiz_data' => $quizData
        ]);

        // Récupérer les quiz précédent et suivant du même chapitre
        $chapitre = $quiz->getChapitre();
        $previousQuiz = null;
        $nextQuiz = null;
        
        if ($chapitre) {
            $allQuizzes = $chapitre->getQuizzes()->toArray();
            // Filtrer uniquement les quiz actifs
            $allQuizzes = array_filter($allQuizzes, fn($q) => $q->getEtat() === 'actif');
            // Réindexer le tableau
            $allQuizzes = array_values($allQuizzes);
            
            // Trouver l'index du quiz actuel
            $currentIndex = array_search($quiz, $allQuizzes, true);
            
            if ($currentIndex !== false) {
                // Quiz précédent
                if ($currentIndex > 0) {
                    $previousQuiz = $allQuizzes[$currentIndex - 1];
                }
                // Quiz suivant
                if ($currentIndex < count($allQuizzes) - 1) {
                    $nextQuiz = $allQuizzes[$currentIndex + 1];
                }
            }
        }

        return $this->render('frontoffice/quiz/passage.html.twig', [
            'quiz' => $quiz,
            'quizData' => $quizData,
            'chapitre' => $chapitre,
            'dureeMaxMinutes' => $quiz->getDureeMaxMinutes(),
            'timestampDebut' => $dateDebut->getTimestamp(),
            'previousQuiz' => $previousQuiz,
            'nextQuiz' => $nextQuiz
        ]);
    }

    #[Route('/{id}/submit', name: 'app_quiz_submit', methods: ['POST'])]
    public function submit(
        Quiz $quiz, 
        Request $request, 
        SessionInterface $session
    ): Response {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            throw $this->createAccessDeniedException();
        }

        $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
        
        // Vérifier qu'il y a une tentative en cours
        if (!$session->has($tentativeKey)) {
            $this->addFlash('error', 'Aucune tentative en cours pour ce quiz.');
            return $this->redirectToRoute('app_frontoffice_quiz_list', [
                'chapitreId' => $quiz->getChapitre()?->getId()
            ]);
        }

        $tentative = $session->get($tentativeKey);
        $dateFin = new \DateTime();
        
        // Calculer la durée réelle
        $dateDebut = \DateTime::createFromFormat('Y-m-d H:i:s', $tentative['date_debut']);
        $dureeReelleSecondes = $dateFin->getTimestamp() - $dateDebut->getTimestamp();
        $dureeReelleMinutes = round($dureeReelleSecondes / 60, 2);
        
        // Vérifier si le temps maximum est dépassé
        $tempsDepasse = false;
        if ($quiz->getDureeMaxMinutes()) {
            $dureeMaxSecondes = $quiz->getDureeMaxMinutes() * 60;
            if ($dureeReelleSecondes > $dureeMaxSecondes) {
                $tempsDepasse = true;
                $this->addFlash('warning', 'Le temps maximum a été dépassé. Soumission automatique effectuée.');
            }
        }
        
        $reponses = $request->request->all('answers');
        
        // Calculer le score
        $result = $this->quizService->calculateScore($quiz, $reponses);
        
        // Enregistrer la tentative
        $this->quizService->enregistrerTentative($etudiant, $quiz, $session, $result);
        
        // Déterminer le statut
        $seuilReussite = $quiz->getSeuilReussite() ?? 50;
        $statut = $result['percentage'] >= $seuilReussite ? 'VALIDÉ' : 'ÉCHEC';
        
        // Nettoyer la tentative en cours
        $session->remove($tentativeKey);
        
        // Sauvegarder les résultats en session pour le tuteur IA
        $resultKey = 'quiz_result_' . $quiz->getId() . '_' . $etudiant->getId();
        $session->set($resultKey, [
            'details' => $result['details'],
            'percentage' => $result['percentage'],
            'score' => $result['score'],
            'timestamp' => time()
        ]);
        
        // Obtenir les statistiques de l'étudiant
        $statistiques = $this->quizService->getStatistiquesEtudiant($etudiant, $quiz, $session);
        
        // Générer les explications IA pour chaque question
        $explications = [];
        $resumePedagogique = [];
        try {
            $explications = $this->correctorAI->genererExplicationsPersonnalisees($result['details']);
            $resumePedagogique = $this->correctorAI->genererResumePedagogique($result['details'], $result['percentage']);
        } catch (\Exception $e) {
            // En cas d'erreur, continuer sans les explications IA
            $this->addFlash('warning', 'Les explications IA ne sont pas disponibles pour le moment.');
        }
        
        return $this->render('frontoffice/quiz/result_with_ai.html.twig', [
            'quiz' => $quiz,
            'result' => $result,
            'statut' => $statut,
            'seuilReussite' => $seuilReussite,
            'chapitre' => $quiz->getChapitre(),
            'dureeReelle' => $dureeReelleMinutes,
            'tempsDepasse' => $tempsDepasse,
            'statistiques' => $statistiques,
            'explications' => $explications,
            'resumePedagogique' => $resumePedagogique
        ]);
    }

    #[Route('/{id}/check-time', name: 'app_quiz_check_time', methods: ['GET'])]
    public function checkTime(Quiz $quiz, SessionInterface $session): JsonResponse
    {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            return new JsonResponse(['error' => 'Non autorisé'], 403);
        }

        $tentativeKey = 'quiz_tentative_' . $quiz->getId() . '_' . $etudiant->getId();
        
        if (!$session->has($tentativeKey)) {
            return new JsonResponse(['error' => 'Aucune tentative en cours'], 404);
        }

        $tentative = $session->get($tentativeKey);
        $now = time();
        $tempsEcoule = $now - $tentative['timestamp_debut'];
        
        $data = [
            'temps_ecoule_secondes' => $tempsEcoule,
            'duree_max_secondes' => $quiz->getDureeMaxMinutes() ? $quiz->getDureeMaxMinutes() * 60 : null,
            'temps_restant_secondes' => null,
            'temps_expire' => false
        ];
        
        if ($quiz->getDureeMaxMinutes()) {
            $dureeMaxSecondes = $quiz->getDureeMaxMinutes() * 60;
            $data['temps_restant_secondes'] = max(0, $dureeMaxSecondes - $tempsEcoule);
            $data['temps_expire'] = $tempsEcoule >= $dureeMaxSecondes;
        }
        
        return new JsonResponse($data);
    }
}
