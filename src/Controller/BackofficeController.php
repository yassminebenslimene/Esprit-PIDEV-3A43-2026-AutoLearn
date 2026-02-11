<?php
// src/Controller/BackofficeController.php

namespace App\Controller;

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
            try {
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.nom LIKE :search')
                    ->orWhere('u.prenom LIKE :search')
                    ->orWhere('u.email LIKE :search')
                    ->setParameter('search', '%' . $search . '%')
                    ->getQuery()
                    ->getResult();
            } catch (\Exception $e) {
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
        $userDto->role = 'ETUDIANT'; // Force role to ETUDIANT
        
        $form = $this->createForm(UserType::class, $userDto, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            $this->addFlash('success', 'Étudiant créé avec succès!');
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->render('backoffice/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Créer un nouvel étudiant',
            'is_edit' => false,
            'hide_role' => true, // 👈 HIDE role field (forced to ETUDIANT)
        ]);
    }

    #[Route('/backoffice/users/{id}/edit', name: 'backoffice_user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUser(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
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
            $this->addFlash('success', 'Étudiant modifié avec succès!');
            return $this->redirectToRoute('backoffice_users');
        }

        return $this->render('backoffice/user_form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier ' . $user->getPrenom() . ' ' . $user->getNom(),
            'user' => $user,
            'is_edit' => true,
            'hide_role' => true, // 👈 HIDE role field (only editing students)
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
        // SIMPLE CHECK: Only allow deleting ETUDIANT users
        if ($user->getRole() !== 'ETUDIANT') {
            $this->addFlash('error', 'Vous ne pouvez supprimer que les étudiants.');
            return $this->redirectToRoute('backoffice_users');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Étudiant supprimé avec succès!');
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

        return $this->render('backoffice/settings.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'isEtudiant' => $user instanceof Etudiant,
            'is_edit' => true,
            'hide_role' => ($user instanceof Etudiant), // 👈 HIDE role for students, SHOW for admins
        ]);
    }

    #[Route('/backoffice/about-templatemo', name: 'backoffice_about_templatemo')]
    public function aboutTemplatemo(): Response
    {
        return $this->render('backoffice/about-templatemo.html.twig');
    }
}