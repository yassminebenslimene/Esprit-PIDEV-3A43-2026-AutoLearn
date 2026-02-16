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
    private string $fromEmail;
    private string $fromName;

    public function __construct(
        MailerInterface $mailer, 
        Environment $twig,
        CertificateService $certificateService
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->certificateService = $certificateService;
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
        string $studentName,
        string $eventName,
        \DateTimeInterface $eventDate,
        string $eventLocation,
        int $participationId
    ): void {
        // Générer le QR code
        $qrCode = QrCode::create('PARTICIPATION-' . $participationId);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrCodeData = $result->getString();
        
        // Générer le fichier .ics pour le calendrier
        $icsContent = $this->generateIcsFile($eventName, $eventDate, $eventLocation);
        
        $html = $this->twig->render('emails/participation_confirmation.html.twig', [
            'studentName' => $studentName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
            'qrCodeData' => base64_encode($qrCodeData),
        ]);

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Participation Confirmed - ' . $eventName)
            ->html($html)
            ->addPart(new DataPart($qrCodeData, 'qrcode.png', 'image/png'))
            ->addPart(new DataPart($icsContent, 'event.ics', 'text/calendar'));

        $this->mailer->send($email);
    }

    /**
     * Envoie un email d'annulation d'événement
     */
    public function sendEventCancellation(
        string $toEmail,
        string $studentName,
        string $eventName
    ): void {
        $html = $this->twig->render('emails/event_cancelled.html.twig', [
            'studentName' => $studentName,
            'eventName' => $eventName,
        ]);

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Event Cancelled - ' . $eventName)
            ->html($html);

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

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Reminder: ' . $eventName . ' in 3 days')
            ->html($html);

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
