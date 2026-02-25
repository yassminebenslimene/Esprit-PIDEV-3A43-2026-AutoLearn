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
        private QuizManagementService $quizService
    ) {}

    #[Route('/{id}/start', name: 'app_quiz_start')]
    public function start(Quiz $quiz, SessionInterface $session): Response
    {
        $etudiant = $this->getUser();
        
        if (!$etudiant instanceof Etudiant) {
            $this->addFlash('error', 'Seuls les étudiants peuvent passer les quiz.');
            return $this->redirectToRoute('app_frontoffice');
        }

        // Vérifier si l'étudiant peut passer le quiz
        $check = $this->quizService->canStudentTakeQuiz($etudiant, $quiz);
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

        return $this->render('frontoffice/quiz/passage.html.twig', [
            'quiz' => $quiz,
            'quizData' => $quizData,
            'chapitre' => $quiz->getChapitre(),
            'dureeMaxMinutes' => $quiz->getDureeMaxMinutes(),
            'timestampDebut' => $dateDebut->getTimestamp()
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
        
        // Déterminer le statut
        $seuilReussite = $quiz->getSeuilReussite() ?? 50;
        $statut = $result['percentage'] >= $seuilReussite ? 'VALIDÉ' : 'ÉCHEC';
        
        // Nettoyer la tentative en cours
        $session->remove($tentativeKey);
        
        return $this->render('frontoffice/quiz/result.html.twig', [
            'quiz' => $quiz,
            'result' => $result,
            'statut' => $statut,
            'seuilReussite' => $seuilReussite,
            'chapitre' => $quiz->getChapitre(),
            'dureeReelle' => $dureeReelleMinutes,
            'tempsDepasse' => $tempsDepasse
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
