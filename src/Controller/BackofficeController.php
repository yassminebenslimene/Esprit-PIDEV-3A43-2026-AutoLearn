<?php
// src/Controller/BackofficeController.php

namespace App\Controller;

use App\Entity\Exercice;
use App\Service\AIExerciseGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\QuizRepository;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Entity\User;
use App\Entity\Etudiant;
use App\Entity\Admin;
use App\Repository\UserRepository;
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BackofficeController extends AbstractController
{
    #[Route('/backoffice', name: 'app_backoffice')]
    public function index(): Response
    {
        return $this->render('backoffice/index.html.twig');
    }

    /**
     * Page de gestion des quiz avec pagination
     */
    #[Route('/backoffice/quiz-management', name: 'backoffice_quiz_management')]
    public function quizManagement(
        QuizRepository $quizRepository,
        Request $request,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $quizRepository->createQueryBuilder('q')
            ->leftJoin('q.chapitre', 'c')
            ->addSelect('c')
            ->orderBy('q.id', 'DESC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('backoffice/quiz_management.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/backoffice/api/quiz/{id}/questions', name: 'backoffice_api_quiz_questions', methods: ['GET'])]
    public function getQuizQuestions(\App\Entity\Quiz $quiz): Response
    {
        $questions = $quiz->getQuestions();
        $data = [];
        
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question->getId(),
                'texteQuestion' => $question->getTexteQuestion(),
                'point' => $question->getPoint(),
                'optionsCount' => $question->getOptions()->count(),
            ];
        }
        
        return $this->json($data);
    }

    #[Route('/backoffice/api/question/{id}/options', name: 'backoffice_api_question_options', methods: ['GET'])]
    public function getQuestionOptions(\App\Entity\Question $question): Response
    {
        $options = $question->getOptions();
        $data = [];
        
        foreach ($options as $option) {
            $data[] = [
                'id' => $option->getId(),
                'texteOption' => $option->getTexteOption(),
                'estCorrecte' => $option->isEstCorrecte(),
            ];
        }
        
        return $this->json($data);
    }

    #[Route('/backoffice/analytics', name: 'backoffice_analytics')]
    public function analytics(): Response
    {
        return $this->render('backoffice/analytics.html.twig');
    }

    #[Route('/backoffice/users', name: 'backoffice_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(UserRepository $userRepository, Request $request): Response
    {
        $allUsers = $userRepository->findAll();

        $totalUsers = count($allUsers);
        $students = array_filter($allUsers, fn($user) => $user->getRole() === 'ETUDIANT');
        $admins = array_filter($allUsers, fn($user) => $user->getRole() === 'ADMIN');

        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $newToday = array_filter($allUsers, fn($user) => $user->getCreatedAt() >= $today);

        $search = $request->query->get('search');
        $roleFilter = $request->query->get('role');

        $users = $allUsers;

        if ($search) {
            try {
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.nom LIKE :search')
                    ->orWhere('u.prenom LIKE :search')
                    ->orWhere('u.email LIKE :search')
                    ->setParameter('search', '%' . $search . '%');

                if ($roleFilter && in_array($roleFilter, ['ADMIN', 'ETUDIANT'])) {
                    $users->andWhere('u.role = :role')
                          ->setParameter('role', $roleFilter);
                }

                $users = $users->getQuery()->getResult();
            } catch (\Exception $e) {
                $users = $allUsers;
            }
        } elseif ($roleFilter && in_array($roleFilter, ['ADMIN', 'ETUDIANT'])) {
            $users = array_filter($allUsers, fn($user) => $user->getRole() === $roleFilter);
        }

        return $this->render('backoffice/users/users.html.twig', [
            'users' => $users,
            'search' => $search,
            'roleFilter' => $roleFilter,
            'totalUsers' => $totalUsers,
            'totalStudents' => count($students),
            'totalAdmins' => count($admins),
            'newTodayCount' => count($newToday),
        ]);
    }

    #[Route('/backoffice/users/new', name: 'backoffice_user_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newUser(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager,
        \App\Service\BrevoMailService $mailService,
        \App\Bundle\UserActivityBundle\Service\ActivityLogger $activityLogger
    ): Response {
        $userDto = new UserCreateDTO();
        $form = $this->createForm(UserType::class, $userDto, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $userDto->password;
            
            if ($userDto->role === 'ADMIN') {
                $user = new Admin();
            } else {
                $user = new Etudiant();
                $user->setNiveau($userDto->niveau);
            }

            $user->setNom($userDto->nom);
            $user->setPrenom($userDto->prenom);
            $user->setEmail($userDto->email);
            $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));
            $user->setRole($userDto->role);

            $entityManager->persist($user);
            $entityManager->flush();

            $activityLogger->logCreate($user);

            try {
                $loginUrl = $this->generateUrl('backoffice_login', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
                $mailService->sendWelcomeEmail(
                    $user->getEmail(),
                    $user->getPrenom() . ' ' . $user->getNom(),
                    $plainPassword,
                    $loginUrl
                );
                $this->addFlash('success', 'Étudiant créé avec succès! Les identifiants ont été envoyés par email.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Étudiant créé mais l\'email n\'a pas pu être envoyé: ' . $e->getMessage());
            }
            $this->addFlash('success', 'Utilisateur créé avec succès!');
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->render('backoffice/users/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouvel utilisateur',
        ]);
    }

    #[Route('/backoffice/users/{id}/edit', name: 'backoffice_user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUser(
        ?User $user, 
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager,
        \App\Bundle\UserActivityBundle\Service\ActivityLogger $activityLogger
    ): Response {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez modifier que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        $userDto = new UserCreateDTO();
        $userDto->nom = $user->getNom();
        $userDto->prenom = $user->getPrenom();
        $userDto->email = $user->getEmail();
        $userDto->role = $user->getRole();
        
        if ($user instanceof Etudiant) {
            $userDto->niveau = $user->getNiveau();
        }
        
        $form = $this->createForm(UserType::class, $userDto, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changes = [];
            
            if ($user->getNom() !== $userDto->nom) {
                $changes['nom'] = ['old' => $user->getNom(), 'new' => $userDto->nom];
            }
            if ($user->getPrenom() !== $userDto->prenom) {
                $changes['prenom'] = ['old' => $user->getPrenom(), 'new' => $userDto->prenom];
            }
            if ($user->getEmail() !== $userDto->email) {
                $changes['email'] = ['old' => $user->getEmail(), 'new' => $userDto->email];
            }
            if ($user instanceof Etudiant && $user->getNiveau() !== $userDto->niveau) {
                $changes['niveau'] = ['old' => $user->getNiveau(), 'new' => $userDto->niveau];
            }
            if ($userDto->password) {
                $changes['password'] = 'changed';
            }
            
            $user->setNom($userDto->nom);
            $user->setPrenom($userDto->prenom);
            $user->setEmail($userDto->email);
            
            if ($userDto->password) {
                $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));
            }
            
            if ($user instanceof Etudiant) {
                $user->setNiveau($userDto->niveau);
            }

            $entityManager->flush();
            
            $activityLogger->logUpdate($user, $changes);
            
            $this->addFlash('success', 'Étudiant modifié avec succès!');
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->render('backoffice/users/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier ' . $user->getPrenom() . ' ' . $user->getNom(),
            'user' => $user,
            'is_edit' => true,
            'hide_role' => true, 
        ]);
    }

    #[Route('/backoffice/users/export', name: 'backoffice_users_export')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        $csvData = "ID,Nom,Prenom,Email,Role,Niveau,Created At\n";
        
        foreach ($users as $user) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $user->getId(),
                $user->getNom(),
                $user->getPrenom(),
                $user->getEmail(),
                $user->getRole(),
                $user->isEtudiant() && $user->getNiveau() ? $user->getNiveau() : 'N/A',
                $user->getCreatedAt()->format('Y-m-d H:i:s')
            );
        }
        
        $response = new Response($csvData);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="users_' . date('Y-m-d') . '.csv"');
        
        return $response;
    }

    #[Route('/backoffice/users/{id}', name: 'backoffice_user_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function showUser(
        ?User $user,
        Request $request,
        \App\Bundle\UserActivityBundle\Service\ActivityLogger $activityLogger
    ): Response {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        $activityLogger->logView($user);
        
        return $this->render('backoffice/users/user_show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/backoffice/users/{id}/suspend', name: 'backoffice_user_suspend', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function suspendUser(
        Request $request, 
        ?User $user, 
        EntityManagerInterface $entityManager,
        \App\Service\BrevoMailService $mailService,
        \App\Bundle\UserActivityBundle\Service\ActivityLogger $activityLogger
    ): Response {
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('backoffice_users');
        }
        
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez suspendre que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        if ($user->getIsSuspended()) {
            $this->addFlash('warning', 'Cet étudiant est déjà suspendu.');
            return $this->redirectToRoute('backoffice_users');
        }

        if ($this->isCsrfTokenValid('suspend' . $user->getId(), $request->request->get('_token'))) {
            $reason = $request->request->get('reason', 'Compte inactif - Inactivité prolongée');
            
            $user->setIsSuspended(true);
            $user->setSuspendedAt(new \DateTime());
            $user->setSuspensionReason($reason);
            $user->setSuspendedBy($this->getUser()->getId());
            
            $entityManager->flush();
            
            $activityLogger->logSuspend($user, $reason);
            
            try {
                $mailService->sendSuspensionEmail(
                    $user->getEmail(),
                    $user->getPrenom() . ' ' . $user->getNom(),
                    $reason
                );
                $this->addFlash('success', 'Étudiant suspendu avec succès! Un email de notification a été envoyé à ' . $user->getEmail());
            } catch (\Exception $e) {
                error_log('Suspension email error: ' . $e->getMessage());
                $this->addFlash('warning', 'Étudiant suspendu mais l\'email n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('backoffice_users');
    }

    #[Route('/backoffice/users/{id}/reactivate', name: 'backoffice_user_reactivate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function reactivateUser(
        Request $request, 
        ?User $user, 
        EntityManagerInterface $entityManager,
        \App\Service\BrevoMailService $mailService,
        \App\Bundle\UserActivityBundle\Service\ActivityLogger $activityLogger
    ): Response {
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('backoffice_users');
        }
        
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez réactiver que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        if (!$user->getIsSuspended()) {
            $this->addFlash('warning', 'Cet étudiant n\'est pas suspendu.');
            return $this->redirectToRoute('backoffice_users');
        }

        if ($this->isCsrfTokenValid('reactivate' . $user->getId(), $request->request->get('_token'))) {
            $user->setIsSuspended(false);
            $user->setSuspendedAt(null);
            $user->setSuspensionReason(null);
            $user->setSuspendedBy(null);
            
            $entityManager->flush();
            
            $activityLogger->logReactivate($user);
            
            try {
                $loginUrl = $this->generateUrl('backoffice_login', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
                $mailService->sendReactivationEmail(
                    $user->getEmail(),
                    $user->getPrenom() . ' ' . $user->getNom(),
                    $loginUrl
                );
                $this->addFlash('success', 'Étudiant réactivé avec succès! Un email de notification a été envoyé à ' . $user->getEmail());
            } catch (\Exception $e) {
                error_log('Reactivation email error: ' . $e->getMessage());
                $this->addFlash('warning', 'Étudiant réactivé mais l\'email n\'a pas pu être envoyé: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('backoffice_users');
    }

    #[Route('/backoffice/settings', name: 'backoffice_settings', methods: ['GET', 'POST'])]
    public function settings(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('backoffice_login');
        }

        $dto = new UserCreateDTO();
        $dto->nom = $user->getNom();
        $dto->prenom = $user->getPrenom();
        $dto->email = $user->getEmail();
        $dto->role = $user->getRole();

        if ($user instanceof Etudiant) {
            $dto->niveau = $user->getNiveau();
        }

        $form = $this->createForm(UserType::class, $dto, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($dto->email !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cet email est déjà utilisé!');
                    return $this->render('backoffice/users/settings.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            }

            $user->setNom($dto->nom);
            $user->setPrenom($dto->prenom);
            $user->setEmail($dto->email);
            
            if ($user instanceof Etudiant && $dto->role === 'ETUDIANT') {
                $user->setNiveau($dto->niveau);
            }

            if (!empty($dto->password)) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $dto->password)
                );
            }

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profil mis à jour avec succès');
                return $this->redirectToRoute('backoffice_settings');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
            }
        }

        return $this->render('backoffice/users/settings.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'isEtudiant' => $user instanceof Etudiant,
        ]);
    }

    #[Route('/backoffice/about-templatemo', name: 'backoffice_about_templatemo')]
    public function aboutTemplatemo(): Response
    {
        return $this->render('backoffice/about-templatemo.html.twig');
    }

    #[Route('/backoffice/login', name: 'backoffice_login')]
    public function login(): Response
    {
        return $this->render('backoffice/login.html.twig');
    }

    #[Route('/backoffice/register', name: 'backoffice_register')]
    public function register(): Response
    {
        return $this->render('backoffice/register.html.twig');
    }

    #[Route('/backoffice/exercices', name: 'backoffice_exercices')]
    public function listExercices(ExerciceRepository $repo): Response
    {
        $exercices = $repo->findAll();

        return $this->render('backoffice/exercice.html.twig', [
            'exercices' => $exercices,
        ]);
    }

    #[Route('/backoffice/exercice/add', name: 'backoffice_exercice_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $exercice = new Exercice();

        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($exercice);
            $em->flush();

            return $this->redirectToRoute('backoffice_exercices');
        }

        return $this->render('backoffice/exercice_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter Exercice'
        ]);
    }

    #[Route('/backoffice/exercice/edit/{id}', name: 'backoffice_exercice_edit')]
    public function edit(
        int $id,
        ExerciceRepository $repo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $exercice = $repo->find($id);

        if (!$exercice) {
            throw $this->createNotFoundException('Exercice non trouvé');
        }

        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('backoffice_exercices');
        }

        return $this->render('backoffice/exercice_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier Exercice'
        ]);
    }

    #[Route('/backoffice/exercice/delete/{id}', name: 'backoffice_exercice_delete')]
    public function delete(
        int $id,
        ExerciceRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $exercice = $repo->find($id);

        if ($exercice) {
            $em->remove($exercice);
            $em->flush();
        }

        return $this->redirectToRoute('backoffice_exercices');
    }
    
    #[Route('/backoffice/exercice/generate-ai', name: 'backoffice_exercice_generate_ai', methods: ['POST'])]
    public function generateExercicesAI(
        Request $request,
        \App\Service\ExerciceGeneratorAIService $generatorService,
        EntityManagerInterface $em
    ): Response {
        try {
            $data = json_decode($request->getContent(), true);
            
            $sujet = $data['sujet'] ?? '';
            $niveau = $data['niveau'] ?? '';
            $nombre = $data['nombre'] ?? 5;
            
            if (empty($sujet) || empty($niveau)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Le sujet et le niveau sont requis'
                ], 400);
            }
            
            $exercicesData = $generatorService->generateExercices($sujet, $niveau, $nombre);
            
            if (empty($exercicesData)) {
                return $this->json([
                    'success' => false,
                    'error' => 'L\'IA n\'a pas pu générer d\'exercices valides. Veuillez réessayer avec un sujet plus précis.'
                ], 500);
            }
            
            $count = 0;
            foreach ($exercicesData as $exerciceData) {
                $exercice = new Exercice();
                $exercice->setQuestion($exerciceData['question']);
                $exercice->setReponse($exerciceData['reponse']);
                $exercice->setPoints($exerciceData['points']);
                
                $em->persist($exercice);
                $count++;
            }
            
            $em->flush();
            
            return $this->json([
                'success' => true,
                'count' => $count,
                'message' => "$count exercice(s) généré(s) avec succès"
            ]);
            
        } catch (\Exception $e) {
            error_log('Erreur génération exercices IA: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'error' => 'Erreur lors de la génération: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/backoffice/challenges', name: 'backoffice_challenges')]
    public function showchallenge(ChallengeRepository $repository): Response
    {
        $challenges = $repository->findAll();

        return $this->render('backoffice/challenge.html.twig', [
            'challenges' => $challenges
        ]);
    }

    #[Route('/backoffice/challenge/add', name: 'backoffice_challenge_add')]
    public function addchall(
        Request $request, 
        EntityManagerInterface $em, 
        Security $security,
        ExerciceRepository $exerciceRepository,
        QuizRepository $quizRepository
    ): Response {
        $challenge = new Challenge();
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        $allExercices = $exerciceRepository->findAll();
        $allQuizs = $quizRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedExerciceIds = $request->request->all('exercices') ?? [];
            
            foreach ($selectedExerciceIds as $exerciceId) {
                $exercice = $exerciceRepository->find($exerciceId);
                if ($exercice) {
                    $challenge->addExercice($exercice);
                }
            }
            
            $selectedQuizIds = $request->request->all('quizs') ?? [];
            
            foreach ($selectedQuizIds as $quizId) {
                $quiz = $quizRepository->find($quizId);
                if ($quiz) {
                    $challenge->addQuiz($quiz);
                }
            }

            $challenge->setCreatedBy($security->getUser());

            $em->persist($challenge);
            $em->flush();

            return $this->redirectToRoute('backoffice_challenges');
        }

        return $this->render('backoffice/challenge_form.html.twig', [
            'form' => $form->createView(),
            'exercices' => $allExercices,
            'quizs' => $allQuizs,
            'exerciceIds' => [],
            'quizIds' => [],
            'title' => 'Ajouter un Challenge'
        ]);
    }

    #[Route('/backoffice/challenge/edit/{id}', name: 'backoffice_challenge_edit')]
    public function editchal(
        $id,
        ChallengeRepository $repository,
        Request $request,
        EntityManagerInterface $em,
        ExerciceRepository $exerciceRepository,
        QuizRepository $quizRepository
    ): Response {
        $challenge = $repository->find($id);

        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }

        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        $allExercices = $exerciceRepository->findAll();
        $allQuizs = $quizRepository->findAll();
        
        $exerciceIds = [];
        foreach ($challenge->getExercices() as $exercice) {
            $exerciceIds[] = $exercice->getId();
        }
        
        $quizIds = [];
        foreach ($challenge->getQuizzes() as $quiz) {
            $quizIds[] = $quiz->getId();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedExerciceIds = $request->request->all('exercices') ?? [];
            
            foreach ($challenge->getExercices() as $exercice) {
                if (!in_array($exercice->getId(), $selectedExerciceIds)) {
                    $challenge->removeExercice($exercice);
                }
            }
            
            foreach ($selectedExerciceIds as $exerciceId) {
                $exercice = $exerciceRepository->find($exerciceId);
                if ($exercice && !$challenge->getExercices()->contains($exercice)) {
                    $challenge->addExercice($exercice);
                }
            }
            
            $selectedQuizIds = $request->request->all('quizs') ?? [];
            
            foreach ($challenge->getQuizzes() as $quiz) {
                if (!in_array($quiz->getId(), $selectedQuizIds)) {
                    $challenge->removeQuiz($quiz);
                }
            }
            
            foreach ($selectedQuizIds as $quizId) {
                $quiz = $quizRepository->find($quizId);
                if ($quiz && !$challenge->getQuizzes()->contains($quiz)) {
                    $challenge->addQuiz($quiz);
                }
            }

            $em->flush();

            return $this->redirectToRoute('backoffice_challenges');
        }

        return $this->render('backoffice/challenge_form.html.twig', [
            'form' => $form->createView(),
            'exercices' => $allExercices,
            'quizs' => $allQuizs,
            'exerciceIds' => $exerciceIds,
            'quizIds' => $quizIds,
            'title' => 'Modifier le Challenge'
        ]);
    }

    #[Route('/backoffice/challenge/delete/{id}', name: 'backoffice_challenge_delete')]
    public function deletechal(
        $id,
        ChallengeRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $challenge = $repository->find($id);

        if (!$challenge) {
            throw $this->createNotFoundException('Challenge non trouvé');
        }

        $em->remove($challenge);
        $em->flush();

        return $this->redirectToRoute('backoffice_challenges');
    }

    #[Route('/backoffice/api/cours/{id}/chapitres', name: 'backoffice_api_cours_chapitres', methods: ['GET'])]
    public function getCoursChapitres(\App\Entity\GestionDeCours\Cours $cours): Response
    {
        $chapitres = $cours->getChapitres();
        $data = [];
        
        foreach ($chapitres as $chapitre) {
            $data[] = [
                'id' => $chapitre->getId(),
                'titre' => $chapitre->getTitre(),
                'ordre' => $chapitre->getOrdre(),
            ];
        }
        
        return $this->json($data);
    }

    #[Route('/backoffice/exercice/ai-generate', name: 'backoffice_exercice_ai_generate', methods: ['POST'])]
    public function aiGenerate(Request $request, AIExerciseGenerator $aiGenerator): JsonResponse
    {
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'error' => 'Vous devez être connecté pour utiliser cette fonctionnalité'
            ], 401);
        }

        try {
            $content = $request->getContent();
            $data = json_decode($content, true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'error' => 'Données JSON invalides: ' . json_last_error_msg()
                ], 400);
            }
            
            $theme = $data['theme'] ?? '';
            $niveau = $data['niveau'] ?? 'Intermédiaire';
            $type = $data['type'] ?? 'open';
            
            if (empty($theme)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Le thème est requis'
                ], 400);
            }
            
            try {
                if ($type === 'qcm') {
                    $exercise = $aiGenerator->generateQCM($theme, $niveau);
                    $exercise['type'] = 'qcm';
                } else {
                    $exercise = $aiGenerator->generateExercise($theme, $niveau);
                    $exercise['type'] = 'open';
                }
            } catch (\Exception $e) {
                $exercise = [
                    'question' => "Question par défaut sur le thème '$theme'",
                    'reponse' => "Réponse par défaut",
                    'points' => 5,
                    'type' => $type
                ];
                
                if ($type === 'qcm') {
                    $exercise['options'] = ['Option 1', 'Option 2', 'Option 3', 'Option 4'];
                    $exercise['bonneReponse'] = 0;
                }
            }
            
            return $this->json([
                'success' => true,
                'exercise' => $exercise
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/backoffice/exercice/ai-save', name: 'backoffice_exercice_ai_save', methods: ['POST'])]
    public function aiSave(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return $this->json([
                    'success' => false,
                    'error' => 'Données invalides'
                ], 400);
            }
            
            $exercice = new Exercice();
            
            if (isset($data['question'])) {
                $exercice->setQuestion($data['question']);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'Question manquante'
                ], 400);
            }
            
            if (isset($data['type']) && $data['type'] === 'qcm') {
                if (isset($data['options']) && isset($data['bonneReponse'])) {
                    $options = implode('|', $data['options']);
                    $exercice->setReponse($options . '||' . $data['bonneReponse']);
                } else {
                    return $this->json([
                        'success' => false,
                        'error' => 'Options du QCM manquantes'
                    ], 400);
                }
            } else {
                if (isset($data['reponse'])) {
                    $exercice->setReponse($data['reponse']);
                } else {
                    return $this->json([
                        'success' => false,
                        'error' => 'Réponse manquante'
                    ], 400);
                }
            }
            
            $exercice->setPoints($data['points'] ?? 5);
            
            $entityManager->persist($exercice);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'id' => $exercice->getId()
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/backoffice/exercice/ai-generate-multiple', name: 'backoffice_exercice_ai_generate_multiple', methods: ['POST'])]
    public function aiGenerateMultiple(Request $request, AIExerciseGenerator $aiGenerator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $theme = $data['theme'] ?? '';
            $niveau = $data['niveau'] ?? 'Intermédiaire';
            $count = min($data['count'] ?? 3, 10);
            
            if (empty($theme)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Le thème est requis'
                ], 400);
            }
            
            $exercises = $aiGenerator->generateMultipleExercises($theme, $niveau, $count);
            
            return $this->json([
                'success' => true,
                'exercises' => $exercises
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}