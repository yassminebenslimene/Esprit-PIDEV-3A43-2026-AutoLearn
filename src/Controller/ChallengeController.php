<?php

namespace App\Controller;
use App\Entity\UserChallenge;
use App\Repository\ChallengeRepository;
<<<<<<< HEAD
use App\Repository\ExerciceRepository;
use App\Repository\QuizRepository;
=======
use App\Repository\UserChallengeRepository;
use App\Service\EmailService;
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
<<<<<<< HEAD

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
=======
use App\Entity\Vote;

class ChallengeController extends AbstractController
{
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
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
<<<<<<< HEAD
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
=======
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
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
    #[Route('/challenge/{id}/complete', name: 'frontchallenge_complete')]
    public function completeChallenge(
        int $id, 
        ChallengeRepository $challengeRepository,
<<<<<<< HEAD
        SessionInterface $session
=======
        SessionInterface $session,
        UserChallengeRepository $userChallengeRepository,
        EntityManagerInterface $entityManager,
        EmailService $emailService
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
    ): Response {
        $challenge = $challengeRepository->find($id);
        
        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }
        
<<<<<<< HEAD
=======
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('backoffice_login');
        }
        
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
        // Récupérer les réponses de la session
        $sessionKey = 'challenge_answers_' . $id;
        $sessionAnswers = $session->get($sessionKey, []);
        
<<<<<<< HEAD
        // Calculer le score total et le score obtenu
        $totalPoints = 0;
        $earnedPoints = 0;
        
        // Vérifier les exercices
=======
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
        
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
        foreach ($challenge->getExercices() as $exercice) {
            $totalPoints += $exercice->getPoints();
            
            if (isset($sessionAnswers[$exercice->getId()])) {
<<<<<<< HEAD
                $userAnswer = $sessionAnswers[$exercice->getId()]['answer'] ?? '';
                // Comparaison insensible à la casse et sans espaces superflus
=======
                $userAnswer = $sessionAnswers[$exercice->getId()];
                if (is_array($userAnswer)) {
                    $userAnswer = $userAnswer['answer'] ?? '';
                }
                
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
                if (strtolower(trim($userAnswer)) === strtolower(trim($exercice->getReponse()))) {
                    $earnedPoints += $exercice->getPoints();
                }
            }
        }
        
<<<<<<< HEAD
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
        
        // Nettoyer la session après completion
=======
        // Marquer comme complété
        $userChallenge->setCompletedAt(new \DateTimeImmutable());
        $userChallenge->setScore($earnedPoints);
        $userChallenge->setTotalPoints($totalPoints);
        $userChallenge->setAnswers($sessionAnswers);
        
        $entityManager->persist($userChallenge);
        $entityManager->flush();
        
        // 🔥 ENVOYER L'EMAIL AVANT DE NETTOYER LA SESSION
        try {
            error_log("=== ENVOI EMAIL CHALLENGE ===");
            error_log("Destinataire: " . $user->getEmail());
            error_log("Challenge: " . $challenge->getTitre());
            
            $emailService->sendChallengeReceipt(
                $user->getEmail(),
                $challenge->getTitre(),
                $earnedPoints,
                $totalPoints,
                new \DateTimeImmutable()
            );
            
            error_log("Email envoyé avec succès !");
            $this->addFlash('success', 'Un récapitulatif vous a été envoyé par email.');
            
        } catch (\Exception $e) {
            error_log("ERREUR ENVOI EMAIL: " . $e->getMessage());
            $this->addFlash('warning', 'Le challenge est terminé mais l\'email n\'a pas pu être envoyé.');
        }
        
        // Nettoyer la session APRÈS l'envoi de l'email
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
        $session->remove($sessionKey);
        
        return $this->render('frontoffice/challenge_complete.html.twig', [
            'challenge' => $challenge,
            'score' => $earnedPoints,
            'totalPoints' => $totalPoints
        ]);
    }
<<<<<<< HEAD
=======
    
    #[Route('/backoffice/challenges/filter', name: 'backoffice_challenges_filter', methods: ['POST'])]
    public function filterChallenges(Request $request, ChallengeRepository $challengeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        error_log('Filtres reçus: ' . print_r($data, true));
        
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
        
        error_log('Nombre de résultats: ' . count($challenges));
        
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
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
}