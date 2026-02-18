<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

class MailerService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private string $fromEmail;
    private string $fromName;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'autolearn66@gmail.com';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'AutoLearn';
    }

    public function sendWelcomeEmail(string $toEmail, string $userName, string $password, string $loginUrl): void
    {
        $htmlContent = $this->getWelcomeHtml($userName, $toEmail, $password, $loginUrl);
        $textContent = $this->getWelcomeText($userName, $toEmail, $password, $loginUrl);

        $this->send($toEmail, $userName, 'Welcome to AutoLearn - Your Account Details', $htmlContent, $textContent);
    }

    public function sendRegistrationConfirmation(string $toEmail, string $userName, string $loginUrl): void
    {
        $htmlContent = $this->getRegistrationHtml($userName, $toEmail, $loginUrl);
        $textContent = $this->getRegistrationText($userName, $toEmail, $loginUrl);

        $this->send($toEmail, $userName, 'Welcome to AutoLearn - Registration Successful', $htmlContent, $textContent);
    }

    private function send(string $toEmail, string $userName, string $subject, string $htmlContent, string $textContent): void
    {
        try {
            $email = (new Email())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to(new Address($toEmail, $userName))
                ->subject($subject)
                ->text($textContent)
                ->html($htmlContent);

            $this->mailer->send($email);
            
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getWelcomeHtml(string $userName, string $email, string $password, string $loginUrl): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to AutoLearn</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f9f9f9; }
        .credentials { background: #fff; border: 2px dashed #667eea; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .password-box { background: #f0f0f0; padding: 10px; font-family: monospace; font-size: 18px; text-align: center; border-radius: 5px; }
        .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px; }
        .warning { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to AutoLearn!</h1>
        </div>
        <div class="content">
            <p>Hello <strong>$userName</strong>,</p>
            <p>Your account has been created. Here are your login details:</p>
            <div class="credentials">
                <h3 style="margin-top: 0;">Your Login Credentials:</h3>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Temporary Password:</strong></p>
                <div class="password-box">$password</div>
            </div>
            <div style="text-align: center;">
                <a href="$loginUrl" class="button">Log In to Your Account</a>
            </div>
            <div class="warning">
                <strong>⚠️ Important:</strong> For security, please change your password immediately after logging in.
            </div>
            <p>Happy learning!<br>The AutoLearn Team</p>
        </div>
        <div class="footer">
            <p>© " . date('Y') . " AutoLearn. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getWelcomeText(string $userName, string $email, string $password, string $loginUrl): string
    {
        return "Welcome to AutoLearn!\n\n" .
               "Hello $userName,\n\n" .
               "Your account has been created. Here are your login details:\n\n" .
               "Email: $email\n" .
               "Temporary Password: $password\n\n" .
               "Login at: $loginUrl\n\n" .
               "⚠️ Important: For security, please change your password immediately after logging in.\n\n" .
               "Happy learning!\n" .
               "The AutoLearn Team";
    }

    private function getRegistrationHtml(string $userName, string $email, string $loginUrl): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to AutoLearn</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f9f9f9; }
        .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to AutoLearn!</h1>
        </div>
        <div class="content">
            <p>Hello <strong>$userName</strong>,</p>
            <p>Thank you for registering! Your account has been successfully created.</p>
            <p>You can now log in to access your learning dashboard:</p>
            <div style="text-align: center;">
                <a href="$loginUrl" class="button">Log In to Your Account</a>
            </div>
            <p><strong>Your login email:</strong> $email</p>
            <p>Happy learning!<br>The AutoLearn Team</p>
        </div>
        <div class="footer">
            <p>© " . date('Y') . " AutoLearn. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getRegistrationText(string $userName, string $email, string $loginUrl): string
    {
        return "Welcome to AutoLearn!\n\n" .
               "Hello $userName,\n\n" .
               "Thank you for registering! Your account has been successfully created.\n\n" .
               "Login at: $loginUrl\n\n" .
               "Your login email: $email\n\n" .
               "Happy learning!\n" .
               "The AutoLearn Team";
    }
}