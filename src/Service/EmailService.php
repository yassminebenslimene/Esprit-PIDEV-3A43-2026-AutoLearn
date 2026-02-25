<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Twig\Environment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private CertificateService $certificateService;
    private BadgeService $badgeService;
    private string $fromEmail;
    private string $fromName;

    public function __construct(
        MailerInterface $mailer, 
        Environment $twig,
        CertificateService $certificateService,
        BadgeService $badgeService
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->certificateService = $certificateService;
        $this->badgeService = $badgeService;
        // L'email de ton sender identity SendGrid
        $this->fromEmail = 'autolearnplateforme@gmail.com';
        $this->fromName = 'Autolearn Platform';
    }

    /**
     * Envoie un email de test
     */
    public function sendTestEmail(string $toEmail): void
    {
        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Test Email - Autolearn Platform')
            ->html('<h1>Test réussi !</h1><p>Ton intégration SendGrid fonctionne parfaitement.</p>');

        $this->mailer->send($email);
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
        
        // Créer le contenu du QR code avec format professionnel
        $qrContent = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $qrContent .= "   EVENT PARTICIPATION\n";
        $qrContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $qrContent .= "PARTICIPANT:\n";
        $qrContent .= "  " . strtoupper($studentName) . "\n\n";
        $qrContent .= "TEAM:\n";
        $qrContent .= "  " . $teamName . "\n\n";
        $qrContent .= "EVENT:\n";
        $qrContent .= "  " . $eventName . "\n\n";
        $qrContent .= "DATE:\n";
        $qrContent .= "  " . $eventDate->format('F d, Y - H:i') . "\n\n";
        $qrContent .= "REGISTRATION ID:\n";
        $qrContent .= "  #" . str_pad($participationId, 6, '0', STR_PAD_LEFT) . "\n\n";
        $qrContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $qrContent .= "✓ Registration Confirmed\n";
        $qrContent .= "   AUTOLEARN PLATFORM\n";
        $qrContent .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
        
        // Générer le QR code via API externe (pas besoin de GD)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrContent);
        
        // Télécharger l'image du QR code
        $qrCodeData = @file_get_contents($qrCodeUrl);
        if ($qrCodeData === false) {
            // Si l'API externe échoue, continuer sans QR code
            $qrCodeData = null;
        }
        
        // Générer le badge PDF
        $badgePdf = $this->badgeService->generateBadge(
            $studentFirstName,
            $studentLastName,
            $teamName,
            $eventName,
            $eventDate
        );
        
        // Générer le fichier .ics pour le calendrier
        $icsContent = $this->generateIcsFile($eventName, $eventDate, $eventLocation);
        
        $html = $this->twig->render('emails/participation_confirmation.html.twig', [
            'studentName' => $studentName,
            'teamName' => $teamName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
            'qrCodeData' => $qrCodeData ? base64_encode($qrCodeData) : null,
        ]);
        
        // Version texte plain pour améliorer la délivrabilité
        $textContent = "PARTICIPATION CONFIRMED\n\n";
        $textContent .= "Hello {$studentName},\n\n";
        $textContent .= "Great news! Your participation has been accepted for the following event:\n\n";
        $textContent .= "Event: {$eventName}\n";
        $textContent .= "Date: " . $eventDate->format('F d, Y \a\t H:i') . "\n";
        $textContent .= "Location: {$eventLocation}\n\n";
        $textContent .= "We're excited to see you there! Please arrive 10 minutes early for check-in.\n\n";
        $textContent .= "Important: Don't forget to bring your student ID.\n\n";
        $textContent .= "Attachments:\n";
        $textContent .= "- event-badge.pdf: Print and wear this badge during the event\n";
        $textContent .= "- event.ics: Add this event to your calendar\n";
        if ($qrCodeData) {
            $textContent .= "- qrcode.png: Your check-in QR code\n";
        }
        $textContent .= "\n---\n";
        $textContent .= "Autolearn Platform - Your Learning Journey\n";
        $textContent .= "This is an automated email, please do not reply.\n";

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->replyTo(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Participation Confirmed - ' . $eventName)
            ->html($html)
            ->text($textContent)
            ->addPart(new DataPart($icsContent, 'event.ics', 'text/calendar'))
            ->addPart(new DataPart($badgePdf, 'event-badge.pdf', 'application/pdf'));
        
        // En-têtes anti-spam
        $email->getHeaders()
            ->addTextHeader('X-Priority', '1')
            ->addTextHeader('X-Mailer', 'Autolearn Platform Mailer')
            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
            ->addTextHeader('List-Unsubscribe', '<mailto:' . $this->fromEmail . '?subject=unsubscribe>');
        
        // Ajouter le QR code seulement s'il a été généré avec succès
        if ($qrCodeData) {
            $email->addPart(new DataPart($qrCodeData, 'qrcode.png', 'image/png'));
        }

        $this->mailer->send($email);
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
        $html = $this->twig->render('emails/event_cancelled.html.twig', [
            'studentName' => $studentName,
            'teamName' => $teamName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
        ]);
        
        // Version texte plain pour améliorer la délivrabilité
        $textContent = "EVENT CANCELLED\n\n";
        $textContent .= "Hello {$studentName},\n\n";
        $textContent .= "We regret to inform you that the following event has been CANCELLED:\n\n";
        $textContent .= "Event: {$eventName}\n";
        $textContent .= "Team: {$teamName}\n";
        $textContent .= "Originally scheduled: " . $eventDate->format('F d, Y \a\t H:i') . "\n";
        $textContent .= "Location: {$eventLocation}\n\n";
        $textContent .= "IMPORTANT NOTICE:\n";
        $textContent .= "This event will NOT take place. Please do not go to the venue.\n\n";
        $textContent .= "What happens next?\n";
        $textContent .= "- Your registration has been automatically cancelled\n";
        $textContent .= "- No action is required from you\n";
        $textContent .= "- Watch for announcements about future events\n";
        $textContent .= "- We apologize for any inconvenience\n\n";
        $textContent .= "We understand this may be disappointing. We'll notify you as soon as new events are scheduled!\n\n";
        $textContent .= "---\n";
        $textContent .= "Autolearn Platform - Your Learning Journey\n";
        $textContent .= "This is an automated email, please do not reply.\n";

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->replyTo(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('⚠️ Event Cancelled - ' . $eventName)
            ->html($html)
            ->text($textContent);
        
        // En-têtes anti-spam
        $email->getHeaders()
            ->addTextHeader('X-Priority', '1')
            ->addTextHeader('X-Mailer', 'Autolearn Platform Mailer')
            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
            ->addTextHeader('List-Unsubscribe', '<mailto:' . $this->fromEmail . '?subject=unsubscribe>');

        $this->mailer->send($email);
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
        
        // Version texte plain pour améliorer la délivrabilité
        $textContent = "EVENT STARTED\n\n";
        $textContent .= "Hello {$studentName},\n\n";
        $textContent .= "The event has officially started!\n\n";
        $textContent .= "Event: {$eventName}\n";
        $textContent .= "Team: {$teamName}\n";
        $textContent .= "Date: " . $eventDate->format('F d, Y \a\t H:i') . "\n";
        $textContent .= "Location: {$eventLocation}\n\n";
        $textContent .= "We hope you have a great experience!\n\n";
        $textContent .= "---\n";
        $textContent .= "Autolearn Platform - Your Learning Journey\n";
        $textContent .= "This is an automated email, please do not reply.\n";

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->replyTo(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('🚀 Event Started - ' . $eventName)
            ->html($html)
            ->text($textContent);
        
        // En-têtes anti-spam
        $email->getHeaders()
            ->addTextHeader('X-Priority', '1')
            ->addTextHeader('X-Mailer', 'Autolearn Platform Mailer')
            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
            ->addTextHeader('List-Unsubscribe', '<mailto:' . $this->fromEmail . '?subject=unsubscribe>');

        $this->mailer->send($email);
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
        
        // Version texte plain pour améliorer la délivrabilité
        $textContent = "EVENT REMINDER\n\n";
        $textContent .= "Hello {$studentName},\n\n";
        $textContent .= "This is a friendly reminder that your event is coming up in 3 days!\n\n";
        $textContent .= "Event: {$eventName}\n";
        $textContent .= "Date: " . $eventDate->format('F d, Y \a\t H:i') . "\n";
        $textContent .= "Location: {$eventLocation}\n\n";
        $textContent .= "Don't forget to:\n";
        $textContent .= "- Bring your student ID\n";
        $textContent .= "- Print your event badge\n";
        $textContent .= "- Arrive 10 minutes early\n\n";
        $textContent .= "We look forward to seeing you there!\n\n";
        $textContent .= "---\n";
        $textContent .= "Autolearn Platform - Your Learning Journey\n";
        $textContent .= "This is an automated email, please do not reply.\n";

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->replyTo(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Reminder: ' . $eventName . ' in 3 days')
            ->html($html)
            ->text($textContent);
        
        // En-têtes anti-spam
        $email->getHeaders()
            ->addTextHeader('X-Priority', '1')
            ->addTextHeader('X-Mailer', 'Autolearn Platform Mailer')
            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
            ->addTextHeader('List-Unsubscribe', '<mailto:' . $this->fromEmail . '?subject=unsubscribe>');

        $this->mailer->send($email);
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
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; margin: -40px -40px 30px -40px;">
            <h1 style="margin: 0;">🎓 Congratulations!</h1>
        </div>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            Dear <strong>{$studentFirstName} {$studentLastName}</strong>,
        </p>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            Thank you for your participation in <strong>{$eventName}</strong>!
        </p>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            We are pleased to provide you with your official certificate of participation. This certificate recognizes your commitment and active involvement in this event.
        </p>
        <div style="background: #f0f4ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <p style="margin: 0; font-size: 14px; color: #555;">
                📄 <strong>Attachment:</strong> certificate.pdf<br>
                💡 <strong>Tip:</strong> You can print this certificate for your records or portfolio.
            </p>
        </div>
        <p style="font-size: 16px; color: #333; line-height: 1.6;">
            We hope you enjoyed the event and learned valuable skills. Stay tuned for more exciting events!
        </p>
        <p style="font-size: 16px; color: #333; line-height: 1.6; margin-top: 30px;">
            Best regards,<br>
            <strong>Autolearn Platform Team</strong>
        </p>
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 12px;">
            <p>Autolearn Platform - Your Learning Journey</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;

        // Version texte plain pour améliorer la délivrabilité
        $textContent = "CERTIFICATE OF PARTICIPATION\n\n";
        $textContent .= "Dear {$studentFirstName} {$studentLastName},\n\n";
        $textContent .= "Congratulations! Thank you for your participation in {$eventName}!\n\n";
        $textContent .= "We are pleased to provide you with your official certificate of participation. ";
        $textContent .= "This certificate recognizes your commitment and active involvement in this event.\n\n";
        $textContent .= "Attachment: certificate.pdf\n";
        $textContent .= "Tip: You can print this certificate for your records or portfolio.\n\n";
        $textContent .= "We hope you enjoyed the event and learned valuable skills. Stay tuned for more exciting events!\n\n";
        $textContent .= "Best regards,\n";
        $textContent .= "Autolearn Platform Team\n\n";
        $textContent .= "---\n";
        $textContent .= "Autolearn Platform - Your Learning Journey\n";
        $textContent .= "This is an automated email, please do not reply.\n";

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->replyTo(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('🎓 Your Certificate - ' . $eventName)
            ->html($html)
            ->text($textContent)
            ->addPart(new DataPart($pdfContent, 'certificate.pdf', 'application/pdf'));
        
        // En-têtes anti-spam
        $email->getHeaders()
            ->addTextHeader('X-Priority', '1')
            ->addTextHeader('X-Mailer', 'Autolearn Platform Mailer')
            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
            ->addTextHeader('List-Unsubscribe', '<mailto:' . $this->fromEmail . '?subject=unsubscribe>');

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
