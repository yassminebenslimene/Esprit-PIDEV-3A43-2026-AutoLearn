<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Etudiant;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'backoffice_login')]
public function login(AuthenticationUtils $authUtils): Response
{
    // Si déjà connecté, rediriger selon le rôle
    if ($this->getUser()) {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur est un Admin
        if ($user instanceof Admin || $user->getRole() === 'ADMIN') {
            return $this->redirectToRoute('app_backoffice');
        }
        
        // Sinon, rediriger vers le frontoffice (pour les étudiants)
        return $this->redirectToRoute('app_frontoffice');
    }

    return $this->render('backoffice/login.html.twig', [
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
        ValidatorInterface $validator
    ): Response {
        $errors = [];
        $oldValues = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'role' => '',
            'niveau' => ''
        ];

        if ($request->isMethod('POST')) {
            // Récupération des données
            $nom = trim($request->request->get('nom', ''));
            $prenom = trim($request->request->get('prenom', ''));
            $email = trim($request->request->get('email', ''));
            $password = $request->request->get('password', '');
            $confirmPassword = $request->request->get('confirm_password', '');
            $role = $request->request->get('role', '');
            $niveau = $request->request->get('niveau', '');

            // Sauvegarde pour pré-remplissage
            $oldValues = compact('nom', 'prenom', 'email', 'role', 'niveau');

            // VALIDATION SERVEUR
            if (empty($nom)) $errors[] = "Le nom est obligatoire";
            if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
            
            if (empty($email)) {
                $errors[] = "L'email est obligatoire";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }
            
            if (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
            }
            
            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            
            if (!in_array($role, ['ADMIN', 'ETUDIANT'])) {
                $errors[] = "Vous devez sélectionner un type de compte";
            }
            
            if ($role === 'ETUDIANT' && !in_array($niveau, ['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'])) {
                $errors[] = "Le niveau est obligatoire pour un étudiant";
            }

            // Vérification email unique
            if (empty($errors)) {
                $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existing) {
                    $errors[] = "Cet email est déjà utilisé";
                }
            }

            // Si aucune erreur, création de l'utilisateur
            if (empty($errors)) {
                if ($role === 'ADMIN') {
                    $user = new Admin();
                } else {
                    $user = new Etudiant();
                    $user->setNiveau($niveau);
                }

                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setEmail($email);
                $user->setRole($role);
                $user->setPassword($hasher->hashPassword($user, $password));

                // Validation Symfony
                $validationErrors = $validator->validate($user);
                if (count($validationErrors) > 0) {
                    foreach ($validationErrors as $error) {
                        $errors[] = $error->getMessage();
                    }
                } else {
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
                    return $this->redirectToRoute('backoffice_login');
                }
            }
        }

        return $this->render('backoffice/register.html.twig', [
            'errors' => $errors,
            'old_values' => $oldValues
        ]);
    }
}