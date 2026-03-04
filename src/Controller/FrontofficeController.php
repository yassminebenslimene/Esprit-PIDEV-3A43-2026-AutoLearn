<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use App\Repository\Cours\CoursRepository;
use App\Repository\EvenementRepository;
use App\Repository\EquipeRepository;
use App\Entity\Admin;
use App\Entity\Etudiant;
use App\Entity\User;
use App\Repository\UserRepository;
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use App\Service\CourseProgressService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FrontofficeController extends AbstractController
{
    #[Route('/', name: 'app_frontoffice')]
    public function index(
        ChallengeRepository $challengeRepository,
        EvenementRepository $evenementRepository,
        EquipeRepository $equipeRepository,
        CoursRepository $coursRepository,
        CourseProgressService $progressService
    ): Response {
        // Si l'utilisateur est connecté
        if ($this->getUser()) {
            $user = $this->getUser();
            
            // Si c'est un admin, rediriger vers le backoffice
            if ($user instanceof Admin || $user->getRoles() === 'ADMIN') {
                return $this->redirectToRoute('app_backoffice');
            }
            
            // Utilisateur connecté (étudiant) - calculer la progression
            $cours = $coursRepository->findAll();
            $challenges = $challengeRepository->findAll();
            $evenements = $evenementRepository->findAll();
            $equipes = $equipeRepository->findAll();
            
            $coursProgress = $progressService->getAllCoursesProgress($user, $cours);
            
            return $this->render('frontoffice/index.html.twig', [
                'cours' => $cours,
                'challenges' => $challenges,
                'evenements' => $evenements,
                'equipes' => $equipes,
                'coursProgress' => $coursProgress,
            ]);
        }
        
        // Sinon, afficher le frontoffice normalement
        $challenges = $challengeRepository->findAll();
        $cours = $coursRepository->findAll();
        
        return $this->render('frontoffice/index.html.twig', [
            'cours' => $cours,
            'challenges' => $challenges
        ]);
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

    #[Route('/communaute', name: 'front_communaute')]
    public function communaute(): Response
    {
        return $this->render('frontoffice/communaute/communaute.html.twig');
    }

    #[Route('/contact', name: 'app_contact', methods: ['POST'])]
    public function contact(
        Request $request,
        \App\Service\BrevoMailService $mailService
    ): Response {
        // Get form data
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $this->addFlash('error', 'Tous les champs sont obligatoires.');
            return $this->redirectToRoute('app_frontoffice', [], Response::HTTP_SEE_OTHER);
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Adresse email invalide.');
            return $this->redirectToRoute('app_frontoffice', [], Response::HTTP_SEE_OTHER);
        }
        
        try {
            // Send email to AutoLearn support
            $mailService->sendContactEmail(
                $name,
                $email,
                $subject,
                $message
            );
            
            $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi du message : ' . $e->getMessage());
        }
        
        return $this->redirectToRoute('app_frontoffice', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('frontoffice/about.html.twig');
    }
}