<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Etudiant;
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\BrevoMailService; // 👈 USE BrevoMailService (WORKING)
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
<<<<<<< HEAD
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        BrevoMailService $mailService // 👈 USE BrevoMailService (WORKING)
    ): Response {
        $dto = new UserCreateDTO();
=======
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher
): Response {
    $dto = new UserCreateDTO();
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f

    $form = $this->createForm(UserType::class, $dto, [
        'is_edit' => false,
    ]);
    $form->handleRequest($request);

<<<<<<< HEAD
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $dto->password;
            if (empty($plainPassword)) {
                $plainPassword = bin2hex(random_bytes(4));
                $dto->password = $plainPassword;
            }
            
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
            $user->setPassword($passwordHasher->hashPassword($user, $dto->password));

            $entityManager->persist($user);
            $entityManager->flush();

            if ($user instanceof Etudiant) {
                try {
                    $loginUrl = $this->generateUrl('backoffice_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    $mailService->sendWelcomeEmail(
                        $user->getEmail(),
                        $user->getPrenom() . ' ' . $user->getNom(),
                        $plainPassword,
                        $loginUrl
                    );
                    $this->addFlash('success', 'Étudiant créé avec succès ! Les identifiants ont été envoyés par email.');
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'Étudiant créé mais l\'email n\'a pas pu être envoyé: ' . $e->getMessage() . '. Mot de passe temporaire: ' . $plainPassword);
                }
            } else {
                $this->addFlash('success', 'Administrateur créé avec succès');
            }
            
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('backoffice/user/new.html.twig', [
            'form' => $form->createView(),
            'hide_role' => false,
        ]);
=======
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
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
    }

    return $this->render('backoffice/user/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(?User $user): Response
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        return $this->render('backoffice/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ?User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
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
                $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cet email est déjà utilisé!');
                    return $this->render('backoffice/user/edit.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
<<<<<<< HEAD
                        'hide_role' => false,
=======
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
                    ]);
                }
            }

            if ($dto->role === 'ETUDIANT' && empty($dto->niveau)) {
                $this->addFlash('error', 'Le niveau est requis pour un étudiant!');
                return $this->render('backoffice/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
<<<<<<< HEAD
                    'hide_role' => false,
=======
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
                ]);
            }

            if (($user instanceof Admin && $dto->role === 'ETUDIANT') ||
                ($user instanceof Etudiant && $dto->role === 'ADMIN')) {
                
                $entityManager->remove($user);
                
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
                $newUser->setPassword($user->getPassword());
                $newUser->setCreatedAt($user->getCreatedAt());
                
                $user = $newUser;
            } else {
                $user->setNom($dto->nom);
                $user->setPrenom($dto->prenom);
                $user->setEmail($dto->email);
                $user->setRole($dto->role);
                
                if ($user instanceof Etudiant && $dto->role === 'ETUDIANT') {
                    $user->setNiveau($dto->niveau);
                }
            }

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
<<<<<<< HEAD
            'hide_role' => false,
=======
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        ?User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_user_index');
        }
        
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