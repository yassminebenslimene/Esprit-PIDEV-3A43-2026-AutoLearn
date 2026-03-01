<?php

namespace App\Controller;

use App\Entity\UserChallenge;
use App\Repository\ChallengeRepository;
use App\Repository\ExerciceRepository;
use App\Repository\QuizRepository;
use App\Repository\UserChallengeRepository;
use App\Service\EmailService;
use App\Entity\Vote;
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
    public function saveAnswer(
        Request $request, 
        SessionInterface $session,
        UserChallengeRepository $userChallengeRepository,
        ChallengeRepository $challengeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json(['success' => false, 'error' => 'JSON invalide'], 400);
            }
            
            $itemId = $data['itemId'] ?? null;
            $answer = $data['answer'] ?? null;
            $challengeId = $data['challengeId'] ?? null;
            $currentIndex = $data['currentIndex'] ?? 0;
            
            if (!$itemId || !$challengeId) {
                return $this->json(['success' => false, 'error' => 'Paramètres manquants'], 400);
            }
            
            $user = $this->getUser();
            if ($user) {
                // Récupérer le challenge
                $challenge = $challengeRepository->find($challengeId);
                
                if ($challenge) {
                    // Sauvegarder dans la base de données
                    $userChallenge = $userChallengeRepository->findOneBy([
                        'user' => $user,
                        'challenge' => $challenge
                    ]);
                    
                    if ($userChallenge && !$userChallenge->isCompleted()) {
                        $answers = $userChallenge->getAnswers() ?? [];
                        $answers[$itemId] = $answer;
                        $userChallenge->setAnswers($answers);
                        $userChallenge->setCurrentIndex($currentIndex);
                        $entityManager->flush();
                    }
                }
            }
            
            // Sauvegarder aussi dans la session
            $sessionKey = 'challenge_answers_' . $challengeId;
            $sessionAnswers = $session->get($sessionKey, []);
            $sessionAnswers[$itemId] = $answer;
            $session->set($sessionKey, $sessionAnswers);
            
            return $this->json(['success' => true, 'message' => 'Réponse sauvegardée avec succès']);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/challenge/vote', name: 'frontchallenge_vote', methods: ['POST'])]
    public function voteChallenge(Request $request, EntityManagerInterface $entityManager, ChallengeRepository $challengeRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $challengeId = $data['challengeId'] ?? null;
            $valeur = $data['valeur'] ?? null;
            
            if (!$challengeId || !$valeur) {
                return $this->json([
                    'success' => false,
                    'error' => 'Paramètres manquants'
                ], 400);
            }
            
            $user = $this->getUser();
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'error' => 'Vous devez être connecté pour voter'
                ], 401);
            }
            
            $challenge = $challengeRepository->find($challengeId);
            if (!$challenge) {
                return $this->json([
                    'success' => false,
                    'error' => 'Challenge non trouvé'
                ], 404);
            }
            
            // Vérifier si l'utilisateur a déjà voté
            $existingVote = $entityManager->getRepository(Vote::class)->findOneBy([
                'user' => $user,
                'challenge' => $challenge
            ]);
            
            if ($existingVote) {
                // Mettre à jour le vote existant
                $existingVote->setValeur($valeur);
                $entityManager->flush();
                
                return $this->json([
                    'success' => true,
                    'message' => 'Votre note a été mise à jour !',
                    'note' => $challenge->getNoteMoyenne(),
                    'nbVotes' => $challenge->getNombreVotes()
                ]);
            }
            
            // Créer un nouveau vote
            $vote = new Vote();
            $vote->setUser($user);
            $vote->setChallenge($challenge);
            $vote->setValeur($valeur);
            
            $entityManager->persist($vote);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Merci pour votre vote !',
                'note' => $challenge->getNoteMoyenne(),
                'nbVotes' => $challenge->getNombreVotes()
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/challenge/save-index', name: 'frontchallenge_save_index', methods: ['POST'])]
    public function saveIndex(
        Request $request,
        UserChallengeRepository $userChallengeRepository,
        ChallengeRepository $challengeRepository,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            
            $challengeId = $data['challengeId'] ?? null;
            $currentIndex = $data['currentIndex'] ?? 0;
            
            if (!$challengeId) {
                return $this->json(['success' => false, 'error' => 'ID du challenge manquant'], 400);
            }
            
            $user = $this->getUser();
            if ($user) {
                $challenge = $challengeRepository->find($challengeId);
                
                if ($challenge) {
                    $userChallenge = $userChallengeRepository->findOneBy([
                        'user' => $user,
                        'challenge' => $challenge
                    ]);
                    
                    if ($userChallenge && !$userChallenge->isCompleted()) {
                        $userChallenge->setCurrentIndex($currentIndex);
                        $entityManager->flush();
                    }
                }
            }
            
            // Sauvegarder aussi dans la session
            $sessionKey = 'challenge_current_index_' . $challengeId;
            $session->set($sessionKey, $currentIndex);
            
            return $this->json(['success' => true, 'index' => $currentIndex]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/challenge/{id}/play/{index}', name: 'frontchallenge_play', defaults: ['index' => 0])]
    public function playChallenge(
        int $id, 
        int $index, 
        ChallengeRepository $challengeRepository,
        SessionInterface $session,
        UserChallengeRepository $userChallengeRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('backoffice_login');
        }
        
        // Vérifier si le challenge a déjà été complété
        $existingUserChallenge = $userChallengeRepository->findOneBy([
            'user' => $user,
            'challenge' => $challenge
        ]);
        
        if ($existingUserChallenge && $existingUserChallenge->isCompleted()) {
            $this->addFlash('error', 'Vous avez déjà complété ce challenge');
            return $this->redirectToRoute('frontchallenge', ['id' => $id]);
        }
        
        // Récupérer ou créer l'enregistrement UserChallenge
        if (!$existingUserChallenge) {
            $existingUserChallenge = new UserChallenge();
            $existingUserChallenge->setUser($user);
            $existingUserChallenge->setChallenge($challenge);
            $existingUserChallenge->setCurrentIndex(0);
            $existingUserChallenge->setAnswers([]);
            $entityManager->persist($existingUserChallenge);
            $entityManager->flush();
        }
        
        // Récupérer l'index de progression depuis la base de données
        $savedIndex = $existingUserChallenge->getCurrentIndex() ?? 0;
        
        // Si l'index demandé est différent de celui sauvegardé
        if ($index != $savedIndex && !$existingUserChallenge->isCompleted()) {
            $index = $savedIndex;
        }
        
        // Récupérer les réponses de la session
        $sessionKey = 'challenge_answers_' . $id;
        $sessionAnswers = $session->get($sessionKey, []);
        
        // Fusionner avec les réponses sauvegardées en base
        $savedAnswers = $existingUserChallenge->getAnswers() ?? [];
        $sessionAnswers = array_merge($savedAnswers, $sessionAnswers);
        $session->set($sessionKey, $sessionAnswers);
        
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

    #[Route('/challenge/{id}/complete', name: 'frontchallenge_complete')]
    public function completeChallenge(
        int $id, 
        ChallengeRepository $challengeRepository,
        SessionInterface $session,
        UserChallengeRepository $userChallengeRepository,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('backoffice_login');
        }
        
        // Récupérer les réponses de la session
        $sessionKey = 'challenge_answers_' . $id;
        $sessionAnswers = $session->get($sessionKey, []);
        
        // Récupérer l'enregistrement UserChallenge
        $userChallenge = $userChallengeRepository->findOneBy([
            'user' => $user,
            'challenge' => $challenge
        ]);
        
        if (!$userChallenge) {
            $userChallenge = new UserChallenge();
            $userChallenge->setUser($user);
            $userChallenge->setChallenge($challenge);
        }
        
        // Si déjà complété, rediriger
        if ($userChallenge->isCompleted()) {
            $this->addFlash('error', 'Ce challenge a déjà été complété');
            return $this->redirectToRoute('frontchallenge', ['id' => $id]);
        }
        
        // Calculer le score
        $totalPoints = 0;
        $earnedPoints = 0;
        
        // Vérifier les exercices
        foreach ($challenge->getExercices() as $exercice) {
            $totalPoints += $exercice->getPoints();
            
            if (isset($sessionAnswers[$exercice->getId()])) {
                $userAnswer = $sessionAnswers[$exercice->getId()];
                if (is_array($userAnswer)) {
                    $userAnswer = $userAnswer['answer'] ?? '';
                }
                if (strtolower(trim($userAnswer)) === strtolower(trim($exercice->getReponse()))) {
                    $earnedPoints += $exercice->getPoints();
                }
            }
        }
        
        // Vérifier les quiz
        foreach ($challenge->getQuizzes() as $quiz) {
            foreach ($quiz->getQuestions() as $question) {
                $totalPoints += $question->getPoint();
                
                if (isset($sessionAnswers[$question->getId()])) {
                    $userAnswerOptionId = $sessionAnswers[$question->getId()]['answer'] ?? null;
                    
                    // Trouver l'option sélectionnée et vérifier si elle est correcte
                    foreach ($question->getOptions() as $option) {
                        if ($option->getId() == $userAnswerOptionId && $option->isEstCorrecte()) {
                            $earnedPoints += $question->getPoint();
                            break;
                        }
                    }
                }
            }
        }
        
        // Marquer comme complété
        $userChallenge->setCompletedAt(new \DateTimeImmutable());
        $userChallenge->setScore($earnedPoints);
        $userChallenge->setTotalPoints($totalPoints);
        $userChallenge->setAnswers($sessionAnswers);
        
        $entityManager->persist($userChallenge);
        $entityManager->flush();
        
        // ENVOYER L'EMAIL
        try {
            $emailService->sendChallengeReceipt(
                $user->getEmail(),
                $challenge->getTitre(),
                $earnedPoints,
                $totalPoints,
                new \DateTimeImmutable()
            );
            
            $this->addFlash('success', 'Un récapitulatif vous a été envoyé par email.');
            
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Le challenge est terminé mais l\'email n\'a pas pu être envoyé.');
        }
        
        // Nettoyer la session
        $session->remove($sessionKey);
        
        return $this->render('frontoffice/challenge_complete.html.twig', [
            'challenge' => $challenge,
            'score' => $earnedPoints,
            'totalPoints' => $totalPoints
        ]);
    }

    #[Route('/backoffice/challenges/filter', name: 'backoffice_challenges_filter', methods: ['POST'])]
    public function filterChallenges(Request $request, ChallengeRepository $challengeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $qb = $challengeRepository->createQueryBuilder('c')
            ->leftJoin('c.createdby', 'u')
            ->addSelect('u');
        
        if (!empty($data['titre'])) {
            $qb->andWhere('c.titre LIKE :titre')
               ->setParameter('titre', '%' . $data['titre'] . '%');
        }
        
        if (!empty($data['niveau'])) {
            $qb->andWhere('c.niveau = :niveau')
               ->setParameter('niveau', $data['niveau']);
        }
        
        if (!empty($data['createdBy'])) {
            $qb->andWhere('u.nom LIKE :createdBy OR u.prenom LIKE :createdBy')
               ->setParameter('createdBy', '%' . $data['createdBy'] . '%');
        }
        
        $challenges = $qb->getQuery()->getResult();
        
        $formattedChallenges = [];
        foreach ($challenges as $challenge) {
            $formattedChallenges[] = [
                'id' => $challenge->getId(),
                'titre' => $challenge->getTitre(),
                'description' => $challenge->getDescription(),
                'dateDebut' => $challenge->getDateDebut()->format('Y-m-d'),
                'dateFin' => $challenge->getDateFin()->format('Y-m-d'),
                'niveau' => $challenge->getNiveau(),
                'createdBy' => $challenge->getCreatedby() ? [
                    'userId' => $challenge->getCreatedby()->getId(),
                    'nom' => $challenge->getCreatedby()->getNom(),
                    'prenom' => $challenge->getCreatedby()->getPrenom()
                ] : null
            ];
        }
        
        return $this->json([
            'success' => true,
            'challenges' => $formattedChallenges,
            'count' => count($formattedChallenges)
        ]);
    }

    // Route pour afficher les détails d'un challenge
    #[Route('/challenge/{id}', name: 'frontchallenge')]
    public function showChallenge(
        int $id, 
        ChallengeRepository $challengeRepository,
        UserChallengeRepository $userChallengeRepository
    ): Response {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
        $userChallenge = null;
        $user = $this->getUser();
        
        if ($user) {
            $userChallenge = $userChallengeRepository->findOneBy([
                'user' => $user,
                'challenge' => $challenge
            ]);
        }
        
        return $this->render('frontoffice/challenge_show.html.twig', [
            'challenge' => $challenge,
            'userChallenge' => $userChallenge
        ]);
    }
}