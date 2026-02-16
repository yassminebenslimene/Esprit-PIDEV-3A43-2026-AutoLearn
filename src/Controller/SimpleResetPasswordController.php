<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class SimpleResetPasswordController extends AbstractController
{
    // Page de demande de réinitialisation
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function request(Request $request): Response
    {
        $resetLink = null;
        $errors = [];
        
        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            
            // VALIDATION COTÉ SERVEUR
            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } else {
                // Vérifier si l'email existe dans la base
                // Dans une vraie application, vous devriez vérifier si l'email existe
                // Mais pour la sécurité, on ne révèle pas si l'email existe ou non
                
                // Générer un token
                $token = Uuid::v4()->__toString();
                
                // Stocker en session (simplifié)
                $request->getSession()->set('reset_email_' . $token, $email);
                $request->getSession()->set('reset_token_expiry_' . $token, time() + 3600);
                
                // Générer le lien
                $resetLink = $this->generateUrl('app_reset_password', 
                    ['token' => $token], 
                    \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                );
                
                // Dans la réalité, vous enverriez cet email
                // Pour le debug, on affiche le lien
                $this->addFlash('info', 'DEBUG MODE: Here is your reset link (would be sent by email):');
            }
            
            // Ajouter les erreurs en flash
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }
        
        return $this->render('backoffice/cnx/request.html.twig', [
            'resetLink' => $resetLink,
        ]);
    }
    
    // Page de réinitialisation avec token
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        string $token
    ): Response {
        // Vérifier le token en session
        $email = $request->getSession()->get('reset_email_' . $token);
        $expiry = $request->getSession()->get('reset_token_expiry_' . $token);
        
        if (!$email || $expiry < time()) {
            $this->addFlash('error', 'Invalid or expired reset link');
            return $this->redirectToRoute('app_forgot_password');
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            $this->addFlash('error', 'User not found');
            return $this->redirectToRoute('app_forgot_password');
        }
        
        $errors = [
            'password' => [],
            'confirm_password' => [],
            'global' => []
        ];
        
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', '');
            $confirm = $request->request->get('confirm_password', '');
            
            // VALIDATION COTÉ SERVEUR UNIQUEMENT
            $isValid = true;
            
            // Validation du mot de passe
            if (empty($password)) {
                $errors['password'][] = 'Password is required';
                $isValid = false;
            } elseif (strlen($password) < 8) {
                $errors['password'][] = 'Password must be at least 8 characters';
                $isValid = false;
            } elseif (!preg_match('/[a-z]/', $password)) {
                $errors['password'][] = 'Password must contain at least one lowercase letter';
                $isValid = false;
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors['password'][] = 'Password must contain at least one uppercase letter';
                $isValid = false;
            } elseif (!preg_match('/\d/', $password)) {
                $errors['password'][] = 'Password must contain at least one number';
                $isValid = false;
            } elseif (!preg_match('/[@$!%*?&]/', $password)) {
                $errors['password'][] = 'Password must contain at least one special character (@$!%*?&)';
                $isValid = false;
            }
            
            // Validation de la confirmation
            if (empty($confirm)) {
                $errors['confirm_password'][] = 'Please confirm your password';
                $isValid = false;
            } elseif ($password !== $confirm) {
                $errors['confirm_password'][] = 'Passwords do not match';
                $isValid = false;
            }
            
            // Si validation réussie
            if ($isValid) {
                // Mettre à jour le mot de passe
                $user->setPassword($hasher->hashPassword($user, $password));
                $em->flush();
                
                // Nettoyer la session
                $request->getSession()->remove('reset_email_' . $token);
                $request->getSession()->remove('reset_token_expiry_' . $token);
                
                $this->addFlash('success', 'Password updated successfully! You can now login.');
                return $this->redirectToRoute('backoffice_login');
            }
        }
        
        return $this->render('backoffice/cnx/reset.html.twig', [
            'token' => $token,
            'email' => $email,
            'errors' => $errors,
        ]);
    }
    
    // Redirection pour /reset-password sans token
    #[Route('/reset-password', name: 'app_reset_password_form')]
    public function resetForm(): Response
    {
        return $this->redirectToRoute('app_forgot_password');
    }
}