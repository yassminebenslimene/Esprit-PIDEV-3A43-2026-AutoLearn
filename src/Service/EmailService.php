<?php

namespace App\Service;

use Twig\Environment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class EmailService
{
    private Environment $twig;
    private CertificateService $certificateService;
    private BadgeService $badgeService;
    private string $fromEmail;
    private string $fromName;
    private QrCodeService $qrCodeService;

    public function __construct(
        Environment $twig, 
        QrCodeService $qrCodeService
    ) {
        $this->twig = $twig;
        $this->qrCodeService = $qrCodeService;
        $this->fromEmail = 'autolearn66@gmail.com';
        $this->fromName = 'Autolearn Platform';
    }

    /**
     * Envoie un email de test
     */
    public function sendTestEmail(string $toEmail): void
    {
        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => 'Test User'
                    ]
                ],
                'subject' => 'Test Email - Autolearn Platform',
                'htmlContent' => '<h1>Test réussi !</h1><p>Ton intégration Brevo fonctionne parfaitement.</p>',
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API');
        }
    }

    /**
     * Envoie un email de confirmation de participation avec QR code et fichier .ics
     */
    public function sendParticipationConfirmation(
        string $toEmail,
        string $studentFirstName,
        string $studentLastName,
        string $teamName,
        string $eventName,
        \DateTimeInterface $eventDate,
        string $eventLocation,
        int $participationId
    ): void {
        $studentName = $studentFirstName . ' ' . $studentLastName;
        
        // Générer le QR code
        $qrCodeData = null;
        try {
            $qrData = $this->qrCodeService->generateParticipationData(
                $participationId,
                $studentName,
                $eventName,
                $eventDate
            );
            $qrCodeData = $this->qrCodeService->generateQrCodeBase64($qrData);
        } catch (\Exception $e) {
            // QR code generation failed, continue without it
        }
        
        $html = $this->twig->render('emails/participation_confirmation.html.twig', [
            'studentName' => $studentName,
            'teamName' => $teamName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
            'qrCodeData' => $qrCodeData,
        ]);

        // Utiliser BrevoMailService avec GuzzleHttp
        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $studentName
                    ]
                ],
                'subject' => 'Participation Confirmed - ' . $eventName,
                'htmlContent' => $html,
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API: ' . $response->getBody()->getContents());
        }
    }

    /**
     * Envoie un email d'annulation d'événement
     */
    public function sendEventCancellation(
        string $toEmail,
        string $studentName,
        string $teamName,
        string $eventName,
        \DateTimeInterface $eventDate,
        string $eventLocation
    ): void {
        try {
            if ($this->logger) {
                $this->logger->info('Début envoi email d\'annulation', [
                    'to' => $toEmail,
                    'event' => $eventName
                ]);
            }
            
            $html = $this->twig->render('emails/event_cancelled.html.twig', [
                'studentName' => $studentName,
                'teamName' => $teamName,
                'eventName' => $eventName,
                'eventDate' => $eventDate,
                'eventLocation' => $eventLocation,
            ]);

        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $studentName
                    ]
                ],
                'subject' => '⚠️ Event Cancelled - ' . $eventName,
                'htmlContent' => $html,
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API');
        }
    }
    
    /**
     * Envoie un email de démarrage d'événement à tous les participants
     */
    public function sendEventStarted(
        string $toEmail,
        string $studentName,
        string $teamName,
        string $eventName,
        \DateTimeInterface $eventDate,
        string $eventLocation
    ): void {
        $html = $this->twig->render('emails/event_started.html.twig', [
            'studentName' => $studentName,
            'teamName' => $teamName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
        ]);

        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $studentName
                    ]
                ],
                'subject' => '🚀 Event Started - ' . $eventName,
                'htmlContent' => $html,
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API');
        }
    }

    /**
     * Envoie un email de rappel 3 jours avant l'événement
     */
    public function sendEventReminder(
        string $toEmail,
        string $studentName,
        string $eventName,
        \DateTimeInterface $eventDate,
        string $eventLocation
    ): void {
        $html = $this->twig->render('emails/event_reminder.html.twig', [
            'studentName' => $studentName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
        ]);

        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $studentName
                    ]
                ],
                'subject' => 'Reminder: ' . $eventName . ' in 3 days',
                'htmlContent' => $html,
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API');
        }
    }

    /**
     * Envoie un email d'invitation à rejoindre une communauté
     */
    public function sendCommunityInvitation(
        string $toEmail,
        string $memberName,
        string $communityName,
        string $inviterName,
        string $communityUrl
    ): void {
        $html = $this->twig->render('emails/community_invitation.html.twig', [
            'memberName' => $memberName,
            'communityName' => $communityName,
            'inviterName' => $inviterName,
            'communityUrl' => $communityUrl,
        ]);

        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $memberName
                    ]
                ],
                'subject' => 'Invitation à rejoindre ' . $communityName,
                'htmlContent' => $html,
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API');
        }
    }

    /**
     * Envoie un email de confirmation de complétion de challenge
     */
    public function sendChallengeReceipt(
        string $toEmail,
        string $challengeName,
        int $earnedPoints,
        int $totalPoints,
        \DateTimeInterface $completedAt
    ): void {
        $percentage = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        
        $html = $this->twig->render('emails/challenge_receipt.html.twig', [
            'challengeName' => $challengeName,
            'earnedPoints' => $earnedPoints,
            'totalPoints' => $totalPoints,
            'percentage' => $percentage,
            'completedAt' => $completedAt,
        ]);

        $client = new \GuzzleHttp\Client();
        $apiKey = $_ENV['BREVO_API_KEY'] ?? '';
        
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => 'User'
                    ]
                ],
                'subject' => '🎉 Challenge Completed - ' . $challengeName,
                'htmlContent' => $html,
            ],
            'timeout' => 10
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to send email via Brevo API');
        }
    }
    
    /**
     * Envoie un certificat de participation par email
     */
    public function sendCertificate(
        string $toEmail,
        string $studentFirstName,
        string $studentLastName,
        string $eventName,
        string $eventType,
        \DateTimeInterface $eventDate
    ): void {
        // Générer le certificat PDF
        $pdfContent = $this->certificateService->generateCertificate(
            $studentFirstName,
            $studentLastName,
            $eventName,
            $eventType,
            $eventDate
        );
        
        $html = <<<HTML
<html>
<body style="font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px;">
        <h1 style="color: #667eea; text-align: center;">Congratulations!</h1>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            Dear <strong>{$studentFirstName} {$studentLastName}</strong>,
        </p>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            Thank you for your participation in <strong>{$eventName}</strong>!
        </p>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            Please find attached your certificate of participation. You can print it for your records.
        </p>
        <p style="font-size: 16px; color: #333; line-height: 1.6; margin-top: 30px;">
            Best regards,<br>
            <strong>Autolearn Platform Team</strong>
        </p>
    </div>
</body>
</html>
HTML;

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Your Certificate - ' . $eventName)
            ->html($html)
            ->addPart(new DataPart($pdfContent, 'certificate.pdf', 'application/pdf'));

        $this->mailer->send($email);
    }
    
    /**
     * Génère un fichier .ics pour ajouter l'événement au calendrier
     */
    private function generateIcsFile(
        string $eventName,
        \DateTimeInterface $eventDate,
        string $location
    ): string {
        $dtStart = $eventDate->format('Ymd\THis');
        $dtEnd = (clone $eventDate)->modify('+2 hours')->format('Ymd\THis');
        $now = (new \DateTime())->format('Ymd\THis');
        
        return <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Autolearn Platform//Event//EN
BEGIN:VEVENT
UID:{$now}@autolearn.com
DTSTAMP:{$now}
DTSTART:{$dtStart}
DTEND:{$dtEnd}
SUMMARY:{$eventName}
LOCATION:{$location}
DESCRIPTION:You are registered for this event on Autolearn Platform
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR
ICS;
    }
}

