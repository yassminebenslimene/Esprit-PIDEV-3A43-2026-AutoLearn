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