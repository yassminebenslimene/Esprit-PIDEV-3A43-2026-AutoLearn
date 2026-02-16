<?php

namespace App\Controller;
use App\Repository\ChallengeRepository;
use  App\Entity\Admin;
use App\Entity\Etudiant;
use App\Entity\User;
use App\Repository\UserRepository; // ← AJOUTEZ CET IMPORT
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FrontofficeController extends AbstractController
{
     #[Route('/', name: 'app_frontoffice')]
    public function index(ChallengeRepository $challengeRepository): Response
    {
        // Si l'utilisateur est connecté
        if ($this->getUser()) {
            $user = $this->getUser();
            
            // Si c'est un admin, rediriger vers le backoffice
            if ($user instanceof Admin || $user->getRoles() === 'ADMIN') {
                return $this->redirectToRoute('app_backoffice');
            }
        }
        
        // Sinon, afficher le frontoffice normalement
        $challenges = $challengeRepository->findAll();
        
        // Sinon, afficher le frontoffice normalement avec les challenges
        return $this->render('frontoffice/index.html.twig', [
            'challenges' => $challenges
        ]);
    }

      #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('frontoffice/index.html.twig');
    }

    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        
        // Si pas connecté, rediriger vers login
        if (!$user) {
            return $this->redirectToRoute('backoffice_login');
        }

        // Si admin, rediriger vers backoffice settings
        if ($user instanceof Admin || $user->getRoles() === 'ADMIN') {
            return $this->redirectToRoute('backoffice_settings');
        }

        $dto = new UserCreateDTO();
        $dto->nom = $user->getNom();
        $dto->prenom = $user->getPrenom();
        $dto->email = $user->getEmail();
        $dto->role = $user->getRole();

        $isEtudiant = $user instanceof Etudiant;
        
        if ($isEtudiant) {
            $dto->niveau = $user->getNiveau();
        }

        $form = $this->createForm(UserType::class, $dto, [
            'is_edit' => true,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email a changé et est unique
            if ($dto->email !== $user->getEmail()) {
                $existingUser = $entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $dto->email]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'Cet email est déjà utilisé!');
                    return $this->render('frontoffice/profile.html.twig', [
                        'form' => $form->createView(),
                        'user' => $user,
                        'isEtudiant' => $isEtudiant,
                    ]);
                }
            }

            // Mettre à jour les propriétés
            $user->setNom($dto->nom);
            $user->setPrenom($dto->prenom);
            $user->setEmail($dto->email);
            
            if ($isEtudiant) {
                $user->setNiveau($dto->niveau);
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

                $this->addFlash('success', 'Profil mis à jour avec succès');
                return $this->redirectToRoute('app_profile');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
            }
        }

        return $this->render('frontoffice/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'isEtudiant' => $isEtudiant,
        ]);
    }
}
