<?php
// src/Controller/BackofficeController.php

namespace App\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Exercice;
use App\Repository\QuizRepository;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Entity\User;
use App\Entity\Etudiant;
use App\Entity\Admin;
use App\Repository\UserRepository; // ← AJOUTEZ CET IMPORT
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted; // ← AJOUTEZ CET IMPORT

class BackofficeController extends AbstractController
{
    #[Route('/backoffice', name: 'app_backoffice')]
    public function index(): Response
    {
        return $this->render('backoffice/index.html.twig');
    }

    #[Route('/backoffice/quiz-management', name: 'backoffice_quiz_management')]
    public function quizManagement(\App\Repository\QuizRepository $quizRepository): Response
    {
        return $this->render('backoffice/quiz_management.html.twig', [
            'quizzes' => $quizRepository->findAll(),
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
        // Gérer la recherche
        $search = $request->query->get('search');
        
        if ($search) {
            // Méthode de recherche - créer si elle n'existe pas encore
            try {
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.nom LIKE :search')
                    ->orWhere('u.prenom LIKE :search')
                    ->orWhere('u.email LIKE :search')
                    ->setParameter('search', '%' . $search . '%')
                    ->getQuery()
                    ->getResult();
            } catch (\Exception $e) {
                // Fallback si erreur
                $users = $userRepository->findAll();
            }
        } else {
            $users = $userRepository->findAll();
        }
        
        // Calculer les statistiques
        $totalUsers = count($users);
        $students = array_filter($users, fn($user) => $user->getRole() === 'ETUDIANT');
        $admins = array_filter($users, fn($user) => $user->getRole() === 'ADMIN');
        
        // Utilisateurs créés aujourd'hui
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $newToday = array_filter($users, fn($user) => $user->getCreatedAt() >= $today);
        
        return $this->render('backoffice/users.html.twig', [
            'users' => $users,
            'search' => $search,
            'totalUsers' => $totalUsers,
            'totalStudents' => count($students),
            'totalAdmins' => count($admins),
            'newTodayCount' => count($newToday),
        ]);
    }

    #[Route('/backoffice/users/new', name: 'backoffice_user_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $userDto = new UserCreateDTO();
        $form = $this->createForm(UserType::class, $userDto, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Créer l'utilisateur selon le rôle
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

            $this->addFlash('success', 'Utilisateur créé avec succès!');
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->render('backoffice/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouvel utilisateur',
        ]);
    }

    #[Route('/backoffice/users/{id}/edit', name: 'backoffice_user_edit')]
#[IsGranted('ROLE_ADMIN')]
public function editUser(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    // Créer le DTO manuellement (sans fromEntity)
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
        // Mettre à jour l'utilisateur
        $user->setNom($userDto->nom);
        $user->setPrenom($userDto->prenom);
        $user->setEmail($userDto->email);
        $user->setRole($userDto->role);
        
        // Gérer le mot de passe (seulement si fourni)
        if ($userDto->password) {
            $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));
        }
        
        // Pour les étudiants, mettre à jour le niveau
        if ($user instanceof Etudiant) {
            $user->setNiveau($userDto->niveau);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur mis à jour avec succès!');
        return $this->redirectToRoute('backoffice_users');
    }

    return $this->render('backoffice/user_form.html.twig', [
        'form' => $form->createView(),
        'title' => 'Modifier ' . $user->getPrenom() . ' ' . $user->getNom(),
        'user' => $user,
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
    public function showUser(User $user): Response
    {
        return $this->render('backoffice/user_show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/backoffice/users/{id}/delete', name: 'backoffice_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur supprimé avec succès!');
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if email changed and is unique
            if ($dto->email !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cet email est déjà utilisé!');
                    return $this->render('backoffice/settings.html.twig', [
                        'form' => $form->createView(),
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

        return $this->render('backoffice/settings.html.twig', [
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
): Response
{
    $challenge = new Challenge();
    $form = $this->createForm(ChallengeType::class, $challenge);
    $form->handleRequest($request);

    // Récupérer tous les exercices et quiz disponibles
    $allExercices = $exerciceRepository->findAll();
    $allQuizs = $quizRepository->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les exercices sélectionnés depuis la requête
        $selectedExercices = $request->request->all('exercices') ?? [];
        foreach ($selectedExercices as $exerciceId) {
            $exercice = $exerciceRepository->find($exerciceId);
            if ($exercice) {
                $challenge->addExercice($exercice);
            }
        }
        
        // Récupérer les quiz sélectionnés depuis la requête
        $selectedQuizs = $request->request->all('quizs') ?? [];
        foreach ($selectedQuizs as $quizId) {
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

    // Récupérer tous les exercices et quiz disponibles
    $allExercices = $exerciceRepository->findAll();
    $allQuizs = $quizRepository->findAll();
    
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
        'title' => 'Modifier le Challenge',
        'exercices' => $allExercices,
        'quizs' => $allQuizs,
        'challenge' => $challenge,
        'exerciceIds' => $exerciceIds,
        'quizIds' => $quizIds
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
}