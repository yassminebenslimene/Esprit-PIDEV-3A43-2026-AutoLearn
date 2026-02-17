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
use GuzzleHttp\Client;

class SimpleResetPasswordController extends AbstractController
{
    // Page de demande de réinitialisation
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function request(Request $request, EntityManagerInterface $em): Response
    {
        $errors = [];
        
        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            
            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } else {
                $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if ($user) {
                    $token = Uuid::v4()->__toString();
                    
                    $request->getSession()->set('reset_email_' . $token, $email);
                    $request->getSession()->set('reset_token_expiry_' . $token, time() + 3600);
                    
                    $resetLink = $this->generateUrl('app_reset_password', 
                        ['token' => $token], 
                        \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                    );
                    
                    try {
                        $this->sendBrevoEmail($email, $user->getPrenom() . ' ' . $user->getNom(), $resetLink);
                        $this->addFlash('success', 'If your email exists in our system, you will receive a password reset link.');
                    } catch (\Exception $e) {
                        error_log('Brevo email error: ' . $e->getMessage());
                        $this->addFlash('success', 'If your email exists in our system, you will receive a password reset link.');
                    }
                } else {
                    $this->addFlash('success', 'If your email exists in our system, you will receive a password reset link.');
                }
            }
            
            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }
        
        return $this->render('backoffice/cnx/request.html.twig', [
            'resetLink' => null,
        ]);
    }
    
    /**
     * Send email via Brevo API
     */
    private function sendBrevoEmail(string $toEmail, string $userName, string $resetLink): void
    {
        $client = new Client();
        
        $brevoApiKey = $_ENV['BREVO_API_KEY'] ?? '';
        $fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'autolearn66@gmail.com';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'AutoLearn';
        
        if (empty($brevoApiKey)) {
            throw new \Exception('Brevo API key is not configured');
        }
        
        $htmlContent = $this->renderView('backoffice/cnx/email_template.html.twig', [
            'resetLink' => $resetLink,
            'userName' => $userName
        ]);
        
        $textContent = "Hello $userName,\n\n";
        $textContent .= "We received a request to reset your password. Click the link below to create a new password:\n\n";
        $textContent .= "$resetLink\n\n";
        $textContent .= "This link will expire in 1 hour.\n\n";
        $textContent .= "If you didn't request this, please ignore this email.\n";
        $textContent .= "AutoLearn Team";
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $brevoApiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $fromName,
                    'email' => $fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $userName
                    ]
                ],
                'subject' => 'Password Reset Request - AutoLearn',
                'htmlContent' => $htmlContent,
                'textContent' => $textContent,
                'replyTo' => [
                    'email' => $fromEmail,
                    'name' => $fromName
                ]
            ],
            'timeout' => 10
        ]);
        
        $statusCode = $response->getStatusCode();
        
        if ($statusCode !== 201) {
            throw new \Exception("Brevo API returned status code: $statusCode");
        }
    }
    
    // Page de réinitialisation avec token
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        string $token
    ): Response {
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
            
            $isValid = true;
            
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
            
            if (empty($confirm)) {
                $errors['confirm_password'][] = 'Please confirm your password';
                $isValid = false;
            } elseif ($password !== $confirm) {
                $errors['confirm_password'][] = 'Passwords do not match';
                $isValid = false;
            }
            
            if ($isValid) {
                $user->setPassword($hasher->hashPassword($user, $password));
                $em->flush();
                
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