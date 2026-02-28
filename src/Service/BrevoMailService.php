<?php

namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class BrevoMailService
{
    private string $apiKey;
    private string $fromEmail;
    private string $fromName;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        $this->fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'autolearn66@gmail.com';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'AutoLearn';
        $this->logger = $logger;
    }

    /**
     * Send welcome email with credentials (for admin-created students)
     */
    public function sendWelcomeEmail(string $toEmail, string $userName, string $password, string $loginUrl): void
    {
        $this->logger->info('Attempting to send welcome email', [
            'to' => $toEmail,
            'userName' => $userName
        ]);

        $this->sendBrevoEmail(
            $toEmail,
            $userName,
            'Welcome to AutoLearn - Your Account Details',
            'emails/welcome.html.twig',
            'emails/welcome.txt.twig',
            [
                'userName' => $userName,
                'email' => $toEmail,
                'password' => $password,
                'loginUrl' => $loginUrl
            ]
        );
    }

    /**
     * Send registration confirmation email (for self-registered users)
     */
    public function sendRegistrationConfirmation(string $toEmail, string $userName, string $loginUrl): void
    {
        $this->logger->info('Attempting to send registration confirmation', [
            'to' => $toEmail,
            'userName' => $userName
        ]);

        $this->sendBrevoEmail(
            $toEmail,
            $userName,
            'Welcome to AutoLearn - Registration Successful',
            'emails/registration_confirmation.html.twig',
            'emails/registration_confirmation.txt.twig',
            [
                'userName' => $userName,
                'email' => $toEmail,
                'loginUrl' => $loginUrl
            ]
        );
    }

    /**
     * Send contact form email to AutoLearn support
     */
    public function sendContactEmail(string $senderName, string $senderEmail, string $subject, string $message): void
    {
        $this->logger->info('Attempting to send contact form email', [
            'from' => $senderEmail,
            'senderName' => $senderName,
            'subject' => $subject
        ]);

        if (empty($this->apiKey)) {
            $this->logger->error('Brevo API key is not configured');
            throw new \Exception('Brevo API key is not configured');
        }

        $client = new Client();

        // Create HTML email content
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .info-box { background: white; border-left: 4px solid #667eea; padding: 15px; margin: 15px 0; border-radius: 5px; }
                .label { font-weight: bold; color: #667eea; }
                .message-box { background: white; padding: 20px; border-radius: 5px; margin-top: 20px; border: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📧 Nouveau Message de Contact</h1>
                    <p>AutoLearn Platform</p>
                </div>
                <div class='content'>
                    <div class='info-box'>
                        <p><span class='label'>De:</span> {$senderName}</p>
                    </div>
                    <div class='info-box'>
                        <p><span class='label'>Email:</span> {$senderEmail}</p>
                    </div>
                    <div class='info-box'>
                        <p><span class='label'>Sujet:</span> {$subject}</p>
                    </div>
                    <div class='message-box'>
                        <p class='label'>Message:</p>
                        <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    </div>
                    <p style='margin-top: 30px; color: #718096; font-size: 14px;'>
                        <strong>Note:</strong> Répondez directement à {$senderEmail} pour contacter l'expéditeur.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Create text version
        $textContent = "Nouveau Message de Contact - AutoLearn\n\n";
        $textContent .= "De: {$senderName}\n";
        $textContent .= "Email: {$senderEmail}\n";
        $textContent .= "Sujet: {$subject}\n\n";
        $textContent .= "Message:\n{$message}\n\n";
        $textContent .= "---\n";
        $textContent .= "Répondez directement à {$senderEmail} pour contacter l'expéditeur.";

        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $this->fromEmail // Use platform email as sender
            ],
            'to' => [
                [
                    'email' => $this->fromEmail, // Send to AutoLearn support
                    'name' => 'AutoLearn Support'
                ]
            ],
            'subject' => "Contact Form: {$subject}",
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
            'replyTo' => [
                'email' => $senderEmail, // Reply goes to the person who filled the form
                'name' => $senderName
            ]
        ];

        $this->logger->info('Sending contact form email to Brevo', ['payload' => json_encode($payload)]);

        try {
            $response = $client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 10
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            $this->logger->info('Brevo API response for contact form', [
                'status' => $statusCode,
                'body' => $responseBody
            ]);
            
            if ($statusCode !== 201) {
                throw new \Exception("Brevo API returned status code: {$statusCode} - Response: {$responseBody}");
            }
            
        } catch (\Exception $e) {
            $this->logger->error('Brevo API error for contact form', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send account suspension notification email
     */
    public function sendSuspensionEmail(string $toEmail, string $userName, string $reason): void
    {
        $this->logger->info('Attempting to send suspension notification', [
            'to' => $toEmail,
            'userName' => $userName
        ]);

        $this->sendBrevoEmail(
            $toEmail,
            $userName,
            'Account Suspended - AutoLearn Platform',
            'emails/suspension.html.twig',
            'emails/suspension.txt.twig',
            [
                'userName' => $userName,
                'email' => $toEmail,
                'reason' => $reason,
                'supportEmail' => $this->fromEmail
            ]
        );
    }

    /**
     * Send account reactivation notification email
     */
    public function sendReactivationEmail(string $toEmail, string $userName, string $loginUrl): void
    {
        $this->logger->info('Attempting to send reactivation notification', [
            'to' => $toEmail,
            'userName' => $userName
        ]);

        $this->sendBrevoEmail(
            $toEmail,
            $userName,
            'Account Reactivated - AutoLearn Platform',
            'emails/reactivation.html.twig',
            'emails/reactivation.txt.twig',
            [
                'userName' => $userName,
                'email' => $toEmail,
                'loginUrl' => $loginUrl
            ]
        );
    }

    /**
     * Send admin notification for automatic suspension due to inactivity
     */
    public function sendAdminNotificationInactiveSuspension(
        string $adminEmail,
        string $adminName,
        string $studentName,
        string $studentEmail,
        int $inactiveDays
    ): void {
        $this->logger->info('Attempting to send admin notification for inactive suspension', [
            'to' => $adminEmail,
            'student' => $studentEmail
        ]);

        $this->sendBrevoEmail(
            $adminEmail,
            $adminName,
            'Suspension Automatique - Étudiant Inactif - AutoLearn',
            'emails/admin_inactive_notification.html.twig',
            'emails/admin_inactive_notification.txt.twig',
            [
                'adminName' => $adminName,
                'studentName' => $studentName,
                'studentEmail' => $studentEmail,
                'inactiveDays' => $inactiveDays
            ]
        );
    }

    /**
     * Generic method to send emails via Brevo API
     */
    private function sendBrevoEmail(
        string $toEmail, 
        string $userName, 
        string $subject,
        string $htmlTemplate,
        string $textTemplate,
        array $templateData
    ): void {
        if (empty($this->apiKey)) {
            $this->logger->error('Brevo API key is not configured');
            throw new \Exception('Brevo API key is not configured');
        }

        $this->logger->info('API Key present (first 10 chars): ' . substr($this->apiKey, 0, 10) . '...');

        $client = new Client();

        // Render HTML content
        $htmlContent = $this->renderTemplate($htmlTemplate, $templateData);
        $this->logger->info('HTML template rendered', ['length' => strlen($htmlContent)]);
        
        // Render text content
        $textContent = $this->renderTemplate($textTemplate, $templateData);
        $this->logger->info('Text template rendered', ['length' => strlen($textContent)]);

        $payload = [
            'sender' => [
                'name' => $this->fromName,
                'email' => $this->fromEmail
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $userName
                ]
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
            'replyTo' => [
                'email' => $this->fromEmail,
                'name' => $this->fromName
            ]
        ];

        $this->logger->info('Sending request to Brevo', ['payload' => json_encode($payload)]);

        try {
            $response = $client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 10
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            $this->logger->info('Brevo API response', [
                'status' => $statusCode,
                'body' => $responseBody
            ]);
            
            if ($statusCode !== 201) {
                throw new \Exception("Brevo API returned status code: $statusCode - Response: $responseBody");
            }
            
        } catch (\Exception $e) {
            $this->logger->error('Brevo API error', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Simple template renderer (since we can't use Twig in service)
     */
    private function renderTemplate(string $template, array $data): string
    {
        // For HTML templates, we'll load from file
        $templatePath = __DIR__ . '/../../templates/' . $template;
        $this->logger->info('Looking for template', ['path' => $templatePath]);
        
        if (file_exists($templatePath)) {
            $content = file_get_contents($templatePath);
            $this->logger->info('Template found', ['size' => strlen($content)]);
            
            // Simple variable replacement
            foreach ($data as $key => $value) {
                $content = str_replace('{{ ' . $key . ' }}', $value, $content);
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
            
            // Replace date filter (Twig syntax that won't work with simple replacement)
            $content = str_replace('{{ "now"|date("Y") }}', date('Y'), $content);
            
            return $content;
        }
        
        $this->logger->error('Template not found', ['path' => $templatePath]);
        return '';
    }
}