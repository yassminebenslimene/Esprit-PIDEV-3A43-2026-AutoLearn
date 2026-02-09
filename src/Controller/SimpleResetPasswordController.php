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
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            
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
            
            $this->addFlash('info', 'DEBUG MODE: Here is your reset link (would be sent by email):');
        }
        
        return $this->render('backoffice/request.html.twig', [
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
        
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirm = $request->request->get('confirm_password');
            
            // Validation
            if (strlen($password) < 6) {
                $this->addFlash('error', 'Password must be at least 6 characters');
            } elseif ($password !== $confirm) {
                $this->addFlash('error', 'Passwords do not match');
            } else {
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
        
        return $this->render('backoffice/reset.html.twig', [
            'token' => $token,
            'email' => $email,
        ]);
    }
    
    // Redirection pour /reset-password sans token
    #[Route('/reset-password', name: 'app_reset_password_form')]
    public function resetForm(): Response
    {
        return $this->redirectToRoute('app_forgot_password');
    }
}