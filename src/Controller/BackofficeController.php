<?php
// src/Controller/BackofficeController.php

namespace App\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Repository\QuizRepository;
use App\Entity\User;
use App\Entity\Etudiant;
use App\Entity\Admin;
use App\Entity\GestionDeCours\Cours;
use App\Entity\GestionDeCours\Chapitre;
use App\Entity\Quiz;
use App\Entity\Evenement;
use App\Entity\Communaute;
use App\Entity\Post;
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
    public function index(
        EntityManagerInterface $entityManager
    ): Response
    {
        // Fetch real platform statistics
        $stats = [
            'totalUsers' => $entityManager->getRepository(User::class)->count([]),
            'totalEtudiants' => $entityManager->getRepository(Etudiant::class)->count([]),
            'totalAdmins' => $entityManager->getRepository(Admin::class)->count([]),
            'totalCours' => $entityManager->getRepository(Cours::class)->count([]),
            'totalChapitres' => $entityManager->getRepository(Chapitre::class)->count([]),
            'totalQuiz' => $entityManager->getRepository(Quiz::class)->count([]),
            'totalChallenges' => $entityManager->getRepository(Challenge::class)->count([]),
            'totalExercices' => $entityManager->getRepository(Exercice::class)->count([]),
            'totalEvenements' => $entityManager->getRepository(Evenement::class)->count([]),
            'totalCommunautes' => $entityManager->getRepository(Communaute::class)->count([]),
            'totalPosts' => $entityManager->getRepository(Post::class)->count([]),
        ];
        
        // Recent activity - last 5 users
        $recentUsers = $entityManager->getRepository(User::class)
            ->findBy([], ['id' => 'DESC'], 5);
        
        // Active challenges
        $activeChallenges = $entityManager->getRepository(Challenge::class)
            ->findBy([], ['id' => 'DESC'], 5);
        
        return $this->render('backoffice/index.html.twig', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'activeChallenges' => $activeChallenges,
        ]);
    }

    /**
     * Page de gestion des quiz avec pagination
     * 
     * Utilise le bundle KnpPaginator pour afficher les quiz par pages
     * Cela améliore les performances en ne chargeant que quelques quiz à la fois
     */
    #[Route('/backoffice/quiz-management', name: 'backoffice_quiz_management')]
    public function quizManagement(
        \App\Repository\QuizRepository $quizRepository,
        Request $request,
        // Injection du service PaginatorInterface fourni par KnpPaginatorBundle
        // Ce service permet de paginer n'importe quelle requête Doctrine
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): Response
    {
        // Créer la requête de base avec QueryBuilder
        // QueryBuilder permet de construire des requêtes SQL complexes de manière orientée objet
        $queryBuilder = $quizRepository->createQueryBuilder('q')
            // Jointure avec l'entité Chapitre pour éviter les requêtes N+1
            ->leftJoin('q.chapitre', 'c')
            // Ajouter le chapitre dans le SELECT pour le charger en une seule requête
            ->addSelect('c')
            // Trier les quiz par ID décroissant (les plus récents en premier)
            ->orderBy('q.id', 'DESC');

        // Utiliser KnpPaginator pour paginer les résultats
        // Le bundle transforme automatiquement le QueryBuilder en requête paginée
        $pagination = $paginator->paginate(
            // La requête à paginer (QueryBuilder, Query, ou tableau)
            $queryBuilder,
            // Numéro de page actuel (récupéré depuis l'URL ?page=X, défaut: 1)
            $request->query->getInt('page', 1),
            // Nombre d'éléments par page (5 quiz par page)
            // Modifié de 10 à 5 pour un affichage plus compact
            3
        );

        // Passer l'objet pagination au template Twig
        // Le template utilisera knp_pagination_render() pour afficher les liens de pagination
        return $this->render('backoffice/quiz_management.html.twig', [
            // L'objet pagination contient les quiz de la page actuelle + métadonnées (total, pages, etc.)
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
    public function analytics(
        EntityManagerInterface $entityManager
    ): Response
    {
        // Different stats from dashboard - focus on content depth
        $stats = [
            'totalChapitres' => $entityManager->getRepository(Chapitre::class)->count([]),
            'totalExercices' => $entityManager->getRepository(Exercice::class)->count([]),
            'totalPosts' => $entityManager->getRepository(Post::class)->count([]),
            'totalCommunautes' => $entityManager->getRepository(Communaute::class)->count([]),
            'totalQuiz' => $entityManager->getRepository(Quiz::class)->count([]),
            'totalEtudiants' => $entityManager->getRepository(Etudiant::class)->count([]),
            'totalAdmins' => $entityManager->getRepository(Admin::class)->count([]),
            'totalEvenements' => $entityManager->getRepository(Evenement::class)->count([]),
        ];
        
        // Top courses by chapters count (optimized with DTO hydration)
        // Using subquery to avoid setMaxResults with collection join
        $topCoursData = $entityManager->getRepository(Cours::class)
            ->createQueryBuilder('c')
            ->select('NEW App\DTO\TopCoursDTO(c.id, c.titre, COUNT(ch.id))')
            ->leftJoin('c.chapitres', 'ch')
            ->groupBy('c.id, c.titre')
            ->orderBy('COUNT(ch.id)', 'DESC')
            ->getQuery()
            ->setMaxResults(5)
            ->getResult();
        
        // Fetch full entities for display
        $topCours = [];
        foreach ($topCoursData as $dto) {
            $cours = $entityManager->getRepository(Cours::class)->find($dto->id);
            if ($cours) {
                $topCours[] = $cours;
            }
        }
        
        // Recent events
        $recentEvents = $entityManager->getRepository(Evenement::class)
            ->findBy([], ['dateDebut' => 'DESC'], 5);
        
        // Top challenges by exercises count (optimized with DTO hydration)
        // Using subquery to avoid setMaxResults with collection join
        $topChallengesData = $entityManager->getRepository(Challenge::class)
            ->createQueryBuilder('ch')
            ->select('NEW App\DTO\TopChallengeDTO(ch.id, ch.titre, COUNT(ex.id))')
            ->leftJoin('ch.exercices', 'ex')
            ->groupBy('ch.id, ch.titre')
            ->orderBy('COUNT(ex.id)', 'DESC')
            ->getQuery()
            ->setMaxResults(5)
            ->getResult();
        
        // Fetch full entities for display
        $topChallenges = [];
        foreach ($topChallengesData as $dto) {
            $challenge = $entityManager->getRepository(Challenge::class)->find($dto->id);
            if ($challenge) {
                $topChallenges[] = $challenge;
            }
        }
        
        return $this->render('backoffice/analytics.html.twig', [
            'stats' => $stats,
            'topCours' => $topCours,
            'recentEvents' => $recentEvents,
            'topChallenges' => $topChallenges,
        ]);
    }

    #[Route('/backoffice/users', name: 'backoffice_users')]
        #[IsGranted('ROLE_ADMIN')]
        public function users(UserRepository $userRepository, Request $request): Response
        {
            // Get all users for statistics (always show total stats)
            $allUsers = $userRepository->findAll();

            // Calculate statistics from ALL users
            $totalUsers = count($allUsers);
            $students = array_filter($allUsers, fn($user) => $user->getRole() === 'ETUDIANT');
            $admins = array_filter($allUsers, fn($user) => $user->getRole() === 'ADMIN');

            // Users created today
            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            $newToday = array_filter($allUsers, fn($user) => $user->getCreatedAt() >= $today);

            // Handle search and filter
            $search = $request->query->get('search');
            $roleFilter = $request->query->get('role'); // New: role filter

            $users = $allUsers; // Start with all users

            // Apply search filter
            if ($search) {
                try {
                    $users = $userRepository->createQueryBuilder('u')
                        ->where('u.nom LIKE :search')
                        ->orWhere('u.prenom LIKE :search')
                        ->orWhere('u.email LIKE :search')
                        ->setParameter('search', '%' . $search . '%');

                    // Apply role filter to search query if present
                    if ($roleFilter && in_array($roleFilter, ['ADMIN', 'ETUDIANT'])) {
                        $users->andWhere('u.role = :role')
                              ->setParameter('role', $roleFilter);
                    }

                    $users = $users->getQuery()->getResult();
                } catch (\Exception $e) {
                    $users = $allUsers;
                }
            } elseif ($roleFilter && in_array($roleFilter, ['ADMIN', 'ETUDIANT'])) {
                // Apply only role filter if no search
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
    ): Response
    {
        $userDto = new UserCreateDTO();
        $userDto->role = 'ETUDIANT'; // Force role to ETUDIANT
        
        $form = $this->createForm(UserType::class, $userDto, ['is_edit' => false]);
        $form->remove('role');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Store plain password before hashing
            $plainPassword = $userDto->password;
            
            // Always create Etudiant
            $user = new Etudiant();
            $user->setNom($userDto->nom);
            $user->setPrenom($userDto->prenom);
            $user->setEmail($userDto->email);
            $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));
            $user->setRole('ETUDIANT');
            $user->setNiveau($userDto->niveau);

            $entityManager->persist($user);
            $entityManager->flush();

            // Log the user creation activity
            $activityLogger->logCreate($user);

            // Send welcome email with credentials
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
            
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->render('backoffice/users/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouvel étudiant',
            'is_edit' => false,
            'hide_role' => true, // 👈 HIDE role field (forced to ETUDIANT)
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
    ): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // SIMPLE CHECK: Only allow editing ETUDIANT users
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez modifier que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        // Create DTO
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
            
            // Log the update activity
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
        // Optimisation: Utiliser un itérateur pour éviter de charger tous les users en mémoire
        $users = $userRepository->createQueryBuilder('u')
            ->setMaxResults(10000) // Limite de sécurité
            ->getQuery()
            ->toIterable(); // Utilise un itérateur pour économiser la mémoire
        
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
    ): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        // Log the view activity
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
    ): Response
    {
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('backoffice_users');
        }
        
        // Only allow suspending ETUDIANT users
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez suspendre que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        // Check if already suspended
        if ($user->getIsSuspended()) {
            $this->addFlash('warning', 'Cet étudiant est déjà suspendu.');
            return $this->redirectToRoute('backoffice_users');
        }

        if ($this->isCsrfTokenValid('suspend'.$user->getId(), $request->request->get('_token'))) {
            $reason = $request->request->get('reason', 'Compte inactif - Inactivité prolongée');
            
            // Suspend the user
            $user->setIsSuspended(true);
            $user->setSuspendedAt(new \DateTime());
            $user->setSuspensionReason($reason);
            $user->setSuspendedBy($this->getUser()->getId());
            
            $entityManager->flush();
            
            // Log the suspension activity
            $activityLogger->logSuspend($user, $reason);
            
            // Send suspension email
            try {
                $mailService->sendSuspensionEmail(
                    $user->getEmail(),
                    $user->getPrenom() . ' ' . $user->getNom(),
                    $reason
                );
                $this->addFlash('success', 'Étudiant suspendu avec succès! Un email de notification a été envoyé à ' . $user->getEmail());
            } catch (\Exception $e) {
                // Log the full error
                error_log('Suspension email error: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
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
    ): Response
    {
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('backoffice_users');
        }
        
        // Only allow reactivating ETUDIANT users
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez réactiver que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        // Check if not suspended
        if (!$user->getIsSuspended()) {
            $this->addFlash('warning', 'Cet étudiant n\'est pas suspendu.');
            return $this->redirectToRoute('backoffice_users');
        }

        if ($this->isCsrfTokenValid('reactivate'.$user->getId(), $request->request->get('_token'))) {
            // Reactivate the user
            $user->setIsSuspended(false);
            $user->setSuspendedAt(null);
            $user->setSuspensionReason(null);
            $user->setSuspendedBy(null);
            
            $entityManager->flush();
            
            // Log the reactivation activity
            $activityLogger->logReactivate($user);
            
            // Send reactivation email
            try {
                $loginUrl = $this->generateUrl('backoffice_login', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
                $mailService->sendReactivationEmail(
                    $user->getEmail(),
                    $user->getPrenom() . ' ' . $user->getNom(),
                    $loginUrl
                );
                $this->addFlash('success', 'Étudiant réactivé avec succès! Un email de notification a été envoyé à ' . $user->getEmail());
            } catch (\Exception $e) {
                // Log the full error
                error_log('Reactivation email error: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
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
        // Get the currently logged-in user
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('backoffice_login');
        }

        // Hydrate DTO from entity
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
            $form->remove('role');
    if (!$user instanceof Etudiant) {
        $form->remove('niveau');
    }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if email changed and is unique
            if ($dto->email !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cet email est déjà utilisé!');
                    return $this->render('backoffice/users/settings.html.twig', [
                        'form' => $form->createView(),
                        'user' => $user,
                        'isEtudiant' => $user instanceof Etudiant,
                        'is_edit' => true,
                        'hide_role' => ($user instanceof Etudiant), // 👈 HIDE role for students, SHOW for admins
                    ]);
                }
            }

            // Update user properties
            $user->setNom($dto->nom);
            $user->setPrenom($dto->prenom);
            $user->setEmail($dto->email);
            
            if ($user instanceof Etudiant && $dto->role === 'ETUDIANT') {
                $user->setNiveau($dto->niveau);
            }

            // Update password only if provided
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
            'is_edit' => true,
            'hide_role' => true, // 👈 HIDE role for students, SHOW for admins
            'hide_niveau' => !($user instanceof Etudiant), // 👈 HIDE role for students, SHOW for admins
        ]);
    }

    #[Route('/backoffice/about-templatemo', name: 'backoffice_about_templatemo')]
    public function aboutTemplatemo(): Response
    {
        return $this->render('backoffice/about-templatemo.html.twig');
    }

    #[Route('/backoffice/exercices', name: 'backoffice_exercices')]
    public function listExercices(ExerciceRepository $repo): Response
    {
        $exercices = $repo->createQueryBuilder('e')
            ->setMaxResults(100) // Limite de 100 exercices par page
            ->orderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult();

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
            
            // Générer les exercices avec l'IA
            $exercicesData = $generatorService->generateExercices($sujet, $niveau, $nombre);
            
            if (empty($exercicesData)) {
                return $this->json([
                    'success' => false,
                    'error' => 'L\'IA n\'a pas pu générer d\'exercices valides. Cela peut être dû à: 1) Une réponse mal formatée de l\'IA, 2) Des réponses trop courtes, 3) Un problème de connexion. Veuillez réessayer avec un sujet plus précis.'
                ], 500);
            }
            
            // Créer et persister les exercices
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
            // Log l'erreur complète
            error_log('Erreur génération exercices IA: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            return $this->json([
                'success' => false,
                'error' => 'Erreur lors de la génération: ' . $e->getMessage() . '. Vérifiez que votre clé API Groq est valide et que vous avez une connexion internet.'
            ], 500);
        }
    }
    #[Route('/backoffice/challenges', name: 'backoffice_challenges')]
    public function showchallenge(ChallengeRepository $repository): Response
    {
        $challenges = $repository->createQueryBuilder('c')
            ->setMaxResults(50) // Limite de 50 challenges par page
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();

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

        // Récupérer tous les exercices et quiz disponibles (limité à 500 pour performance)
        $allExercices = $exerciceRepository->findAllWithLimit(500);
        $allQuizs = $quizRepository->findAllWithLimit(500);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les exercices sélectionnés depuis la requête
            $selectedExerciceIds = $request->request->all('exercices') ?? [];
            
            // Ajouter les exercices sélectionnés
            foreach ($selectedExerciceIds as $exerciceId) {
                $exercice = $exerciceRepository->find($exerciceId);
                if ($exercice) {
                    $challenge->addExercice($exercice);
                }
            }
            
            // Récupérer les quiz sélectionnés depuis la requête
            $selectedQuizIds = $request->request->all('quizs') ?? [];
            
            // Ajouter les quiz sélectionnés
            foreach ($selectedQuizIds as $quizId) {
                $quiz = $quizRepository->find($quizId);
                if ($quiz) {
                    $challenge->addQuiz($quiz);
                }
            }

            // 🔥 Ici on affecte automatiquement l'utilisateur connecté
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

        // Récupérer tous les exercices et quiz disponibles (limité à 500 pour performance)
        $allExercices = $exerciceRepository->findAllWithLimit(500);
        $allQuizs = $quizRepository->findAllWithLimit(500);
        
        // Récupérer les IDs des exercices déjà associés
        $exerciceIds = [];
        foreach ($challenge->getExercices() as $exercice) {
            $exerciceIds[] = $exercice->getId();
        }
        
        // Récupérer les IDs des quiz déjà associés
        $quizIds = [];
        foreach ($challenge->getQuizzes() as $quiz) {
            $quizIds[] = $quiz->getId();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les exercices sélectionnés depuis la requête
            $selectedExerciceIds = $request->request->all('exercices') ?? [];
            
            // Supprimer les exercices qui ne sont plus sélectionnés
            foreach ($challenge->getExercices() as $exercice) {
                if (!in_array($exercice->getId(), $selectedExerciceIds)) {
                    $challenge->removeExercice($exercice);
                }
            }
            
            // Ajouter les nouveaux exercices sélectionnés
            foreach ($selectedExerciceIds as $exerciceId) {
                $exercice = $exerciceRepository->find($exerciceId);
                if ($exercice && !$challenge->getExercices()->contains($exercice)) {
                    $challenge->addExercice($exercice);
                }
            }
            
            // Récupérer les quiz sélectionnés depuis la requête
            $selectedQuizIds = $request->request->all('quizs') ?? [];
            
            // Supprimer les quiz qui ne sont plus sélectionnés
            foreach ($challenge->getQuizzes() as $quiz) {
                if (!in_array($quiz->getId(), $selectedQuizIds)) {
                    $challenge->removeQuiz($quiz);
                }
            }
            
            // Ajouter les nouveaux quiz sélectionnés
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

    /**
     * API pour récupérer les chapitres d'un cours
     */
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
}
