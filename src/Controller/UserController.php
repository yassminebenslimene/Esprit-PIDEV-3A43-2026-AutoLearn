<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Etudiant;
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/backoffice/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backoffice/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $dto = new UserCreateDTO();

        $form = $this->createForm(UserType::class, $dto, [
            'is_edit' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email existe déjà (la contrainte UniqueEntity s'en charge)
            // Créer l'utilisateur
            if ($dto->role === 'ADMIN') {
                $user = new Admin();
            } else {
                $user = new Etudiant();
                $user->setNiveau($dto->niveau);
            }

            $user->setNom($dto->nom);
            $user->setPrenom($dto->prenom);
            $user->setEmail($dto->email);
            $user->setRole($dto->role);

            $user->setPassword(
                $passwordHasher->hashPassword($user, $dto->password)
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('backoffice/user/new.html.twig', [
            'form' => $form->createView(),
            'hide_role' => false, // 👈 SHOW role field (admin can choose between Admin/Student)
        ]);
    }
    
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('backoffice/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Hydratation du DTO depuis l'entité
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
            // Vérifier si l'email a changé et s'il est unique
            if ($dto->email !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cet email est déjà utilisé!');
                    return $this->render('backoffice/user/edit.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                        'hide_role' => false, // 👈 SHOW role field (admin can change role)
                    ]);
                }
            }

            // Vérifier le niveau pour les étudiants
            if ($dto->role === 'ETUDIANT' && empty($dto->niveau)) {
                $this->addFlash('error', 'Le niveau est requis pour un étudiant!');
                return $this->render('backoffice/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'hide_role' => false, // 👈 SHOW role field (admin can change role)
                ]);
            }

            // GESTION CRITIQUE : Changement de type d'utilisateur (Admin ↔ Étudiant)
            if (($user instanceof Admin && $dto->role === 'ETUDIANT') ||
                ($user instanceof Etudiant && $dto->role === 'ADMIN')) {
                
                // Supprimer l'ancien utilisateur
                $entityManager->remove($user);
                
                // Créer le nouveau type d'utilisateur
                if ($dto->role === 'ADMIN') {
                    $newUser = new Admin();
                } else {
                    $newUser = new Etudiant();
                    $newUser->setNiveau($dto->niveau);
                }
                
                $newUser->setNom($dto->nom);
                $newUser->setPrenom($dto->prenom);
                $newUser->setEmail($dto->email);
                $newUser->setRole($dto->role);
                $newUser->setPassword($user->getPassword()); // Garder le même mot de passe
                $newUser->setCreatedAt($user->getCreatedAt());
                
                $user = $newUser;
            } else {
                // Pas de changement de type, juste mettre à jour les propriétés
                $user->setNom($dto->nom);
                $user->setPrenom($dto->prenom);
                $user->setEmail($dto->email);
                $user->setRole($dto->role);
                
                // Mettre à jour le niveau si c'est un étudiant
                if ($user instanceof Etudiant && $dto->role === 'ETUDIANT') {
                    $user->setNiveau($dto->niveau);
                }
            }

            // Mettre à jour le mot de passe seulement s'il est fourni
            if (!empty($dto->password)) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $dto->password)
                );
            }

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Utilisateur modifié avec succès');
                return $this->redirectToRoute('app_user_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification: ' . $e->getMessage());
            }
        }

        return $this->render('backoffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'hide_role' => false, // 👈 SHOW role field (admin can change role)
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_user_index');
    }
}