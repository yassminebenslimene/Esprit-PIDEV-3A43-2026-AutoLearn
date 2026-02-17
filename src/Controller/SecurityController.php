<?php

namespace App\Controller;

use App\Service\BrevoMailService; // 👈 USE BrevoMailService (WORKING)
use App\Entity\Admin;
use App\Entity\Etudiant;
use App\Entity\User;
use App\DTO\UserCreateDTO;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'backoffice_login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_backoffice');
            }
            return $this->redirectToRoute('app_frontoffice');
        }

        return $this->render('backoffice/cnx/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/logout', name: 'backoffice_logout')]
    public function logout(): void {}

    #[Route('/register', name: 'backoffice_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator,
        BrevoMailService $mailService // 👈 USE BrevoMailService (WORKING)
    ): Response {
        $dto = new UserCreateDTO();
        
        $form = $this->createForm(UserType::class, $dto, [
            'is_edit' => false,
            'action' => $this->generateUrl('backoffice_register'),
            'method' => 'POST',
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $validationGroups = ['Default', 'registration'];
            
            if ($dto->role === 'ETUDIANT') {
                $validationGroups[] = 'niveau_validation';
            }
            
            $errors = $validator->validate($dto, null, $validationGroups);
            
            if (count($errors) === 0) {
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $dto->email]);
                if ($existingUser) {
                    $form->get('email')->addError(new FormError('Cet email est déjà utilisé'));
                }
            }
            
            if (count($errors) === 0 && $form->isValid()) {
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
                $user->setPassword($hasher->hashPassword($user, $dto->password));

                $entityErrors = $validator->validate($user);
                if (count($entityErrors) === 0) {
                    $em->persist($user);
                    $em->flush();

                    try {
                        $loginUrl = $this->generateUrl('backoffice_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
                        $mailService->sendRegistrationConfirmation(
                            $user->getEmail(),
                            $user->getPrenom() . ' ' . $user->getNom(),
                            $loginUrl
                        );
                        $this->addFlash('success', 'Compte créé avec succès ! Un email de confirmation vous a été envoyé.');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', 'Compte créé avec succès mais l\'email n\'a pas pu être envoyé: ' . $e->getMessage());
                    }
                    
                    return $this->redirectToRoute('backoffice_login');
                }
            }
        }

        return $this->render('backoffice/cnx/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}