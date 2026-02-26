<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private string $fromEmail;
    private string $fromName;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
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
     * Envoie un email de confirmation de participation
     */
    public function sendParticipationConfirmation(
        string $toEmail,
        string $studentName,
        string $eventName,
        \DateTimeInterface $eventDate,
        string $eventLocation
    ): void {
        $html = $this->twig->render('emails/participation_confirmation.html.twig', [
            'studentName' => $studentName,
            'eventName' => $eventName,
            'eventDate' => $eventDate,
            'eventLocation' => $eventLocation,
        ]);

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Participation Confirmed - ' . $eventName)
            ->html($html);

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

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('⚠️ Event Cancelled - ' . $eventName)
            ->html($html);

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

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('🚀 Event Started - ' . $eventName)
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

        $email = (new Email())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($toEmail)
            ->subject('Invitation à rejoindre ' . $communityName)
            ->html($html);

        $this->mailer->send($email);
    }
}
