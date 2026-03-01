<?php
// src/Service/EmailService.php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;
    private string $fromEmail;
    private string $fromName;
    private string $adminEmail;

    public function __construct(
        MailerInterface $mailer, 
        Environment $twig,
        LoggerInterface $logger,
        string $adminEmail
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->adminEmail = $adminEmail;
        // L'email de ton sender identity
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

    /**
     * Envoie un récapitulatif de challenge
     */
    public function sendChallengeReceipt(
        string $to, 
        string $challengeTitle, 
        int $score, 
        int $totalPoints, 
        \DateTimeImmutable $completedAt
    ): void {
        try {
            $this->logger->info("Tentative d'envoi d'email à: " . $to);
            
            $percentage = round(($score / $totalPoints) * 100);
            
            $email = (new Email())
                ->from(new Address($this->adminEmail, 'Scholar Platform'))
                ->to($to)
                ->subject($this->getSubject($score, $totalPoints, $percentage) . ' - ' . $challengeTitle)
                ->html($this->getHtmlContent($challengeTitle, $score, $totalPoints, $percentage, $completedAt))
                ->text($this->getTextContent($challengeTitle, $score, $totalPoints, $percentage, $completedAt));

            $this->mailer->send($email);
            
            $this->logger->info('Email envoyé avec succès à ' . $to);
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur EmailService: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Détermine le sujet de l'email en fonction du score
     */
    private function getSubject(int $score, int $totalPoints, int $percentage): string
    {
        if ($percentage >= 80) {
            return '🏆 Félicitations ! Excellent résultat';
        } elseif ($percentage >= 50) {
            return '👍 Bon travail ! Vous avez réussi';
        } elseif ($percentage >= 30) {
            return '📝 Challenge terminé - Vous pouvez mieux faire';
        } else {
            return '🔄 Challenge terminé - Essayez encore';
        }
    }

    /**
     * Contenu HTML pour le récapitulatif de challenge
     */
    private function getHtmlContent(string $challengeTitle, int $score, int $totalPoints, int $percentage, \DateTimeImmutable $completedAt): string
    {
        $color = $this->getScoreColor($percentage);
        $emoji = $this->getScoreEmoji($percentage);
        $message = $this->getScoreMessage($percentage);
        $advice = $this->getScoreAdvice($percentage);
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: "Poppins", Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(105deg, ' . $this->getGradientStart($percentage) . ' 0%, ' . $this->getGradientEnd($percentage) . ' 100%); 
                    color: white; 
                    padding: 30px; 
                    text-align: center; 
                }
                .header h1 {
                    margin: 0;
                    font-size: 32px;
                    font-weight: 600;
                }
                .header .emoji {
                    font-size: 48px;
                    margin-bottom: 10px;
                }
                .content { 
                    background: #f9f9f9; 
                    padding: 40px; 
                }
                .score-circle {
                    width: 150px;
                    height: 150px;
                    margin: 0 auto 20px;
                    background: conic-gradient(' . $color . ' 0% ' . $percentage . '%, #e0e0e0 ' . $percentage . '% 100%);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }
                .score-circle::before {
                    content: "";
                    width: 120px;
                    height: 120px;
                    background: white;
                    border-radius: 50%;
                    position: absolute;
                }
                .score-text {
                    position: relative;
                    font-size: 24px;
                    font-weight: bold;
                    color: ' . $color . ';
                    z-index: 1;
                }
                .score { 
                    font-size: 48px; 
                    font-weight: bold; 
                    color: ' . $color . '; 
                    text-align: center; 
                    margin: 20px 0; 
                }
                .message {
                    font-size: 20px;
                    text-align: center;
                    margin: 20px 0;
                    padding: 15px;
                    background: white;
                    border-radius: 10px;
                    border-left: 4px solid ' . $color . ';
                }
                .advice {
                    font-size: 16px;
                    color: #7a7a7a;
                    text-align: center;
                    margin: 20px 0;
                    padding: 15px;
                    background: white;
                    border-radius: 10px;
                }
                .details { 
                    margin: 30px 0; 
                }
                .detail-item { 
                    margin: 15px 0; 
                    padding: 15px; 
                    background: white; 
                    border-radius: 10px; 
                    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                }
                .detail-item strong {
                    color: #2a2a2a;
                    display: inline-block;
                    width: 150px;
                }
                .footer { 
                    text-align: center; 
                    padding: 20px;
                    background: white;
                    border-top: 1px solid #eee;
                    color: #7a7a7a; 
                    font-size: 12px; 
                }
                .btn {
                    display: inline-block;
                    padding: 12px 30px;
                    background: linear-gradient(105deg, #7fb77e 0%, #4a9b4a 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 25px;
                    margin-top: 20px;
                    font-weight: 500;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(127, 183, 126, 0.3);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="emoji">' . $emoji . '</div>
                    <h1>' . $this->getHeaderTitle($percentage) . '</h1>
                </div>
                <div class="content">
                    <p style="font-size: 18px; text-align: center;">Challenge : <strong>"' . htmlspecialchars($challengeTitle) . '"</strong></p>
                    
                    <div class="score-circle">
                        <div class="score-text">' . $percentage . '%</div>
                    </div>
                    
                    <div class="score">
                        ' . $score . '/' . $totalPoints . '
                    </div>
                    
                    <div class="message">
                        ' . $emoji . ' ' . $message . '
                    </div>
                    
                    <div class="advice">
                        💡 ' . $advice . '
                    </div>
                    
                    <div class="details">
                        <div class="detail-item">
                            <strong>📊 Pourcentage :</strong> ' . $percentage . '%
                        </div>
                        <div class="detail-item">
                            <strong>📅 Date :</strong> ' . $completedAt->format('d/m/Y H:i') . '
                        </div>
                        <div class="detail-item">
                            <strong>🏆 Niveau :</strong> ' . $this->getLevelFromPercentage($percentage) . '
                        </div>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="http://127.0.0.1:8000/" class="btn">
                            Voir plus de challenges
                        </a>
                    </div>
                    
                    <p style="text-align: center; margin-top: 30px; color: #7a7a7a;">
                        ' . $this->getFooterMessage($percentage) . '
                    </p>
                </div>
                <div class="footer">
                    <p>© ' . date('Y') . ' Scholar. Tous droits réservés.</p>
                    <p style="font-size: 11px;">
                        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Contenu texte pour le récapitulatif de challenge
     */
    private function getTextContent(string $challengeTitle, int $score, int $totalPoints, int $percentage, \DateTimeImmutable $completedAt): string
    {
        $emoji = $this->getScoreEmoji($percentage);
        $message = $this->getScoreMessage($percentage);
        $advice = $this->getScoreAdvice($percentage);
        
        return $emoji . " " . strtoupper($message) . " !\n\n" .
               "Challenge : " . $challengeTitle . "\n" .
               "Score : " . $score . "/" . $totalPoints . "\n" .
               "Pourcentage : " . $percentage . "%\n" .
               "Date : " . $completedAt->format('d/m/Y H:i') . "\n" .
               "Niveau : " . $this->getLevelFromPercentage($percentage) . "\n\n" .
               "Conseil : " . $advice . "\n\n" .
               "Continuez à relever de nouveaux défis sur Scholar !\n\n" .
               "---\n" .
               "© " . date('Y') . " Scholar";
    }

    /**
     * Détermine l'emoji selon le pourcentage
     */
    private function getScoreEmoji(int $percentage): string
    {
        if ($percentage >= 80) {
            return '🏆';
        } elseif ($percentage >= 50) {
            return '👍';
        } elseif ($percentage >= 30) {
            return '📝';
        } else {
            return '🔄';
        }
    }

    /**
     * Détermine le message selon le pourcentage
     */
    private function getScoreMessage(int $percentage): string
    {
        if ($percentage >= 80) {
            return 'Excellent travail ! Vous maîtrisez parfaitement ce challenge.';
        } elseif ($percentage >= 50) {
            return 'Bon travail ! Vous avez réussi, mais il y a encore de la marge de progression.';
        } elseif ($percentage >= 30) {
            return 'Challenge terminé. Avec un peu plus de pratique, vous ferez mieux !';
        } else {
            return 'Ce challenge était difficile. Ne lâchez rien, réessayez !';
        }
    }

    /**
     * Détermine le conseil selon le pourcentage
     */
    private function getScoreAdvice(int $percentage): string
    {
        if ($percentage >= 80) {
            return 'Vous êtes un expert ! Essayez maintenant des challenges plus difficiles.';
        } elseif ($percentage >= 50) {
            return 'Vous êtes sur la bonne voie. Révisez les points où vous avez eu des difficultés.';
        } elseif ($percentage >= 30) {
            return 'Ne vous découragez pas. La pratique est la clé du succès.';
        } else {
            return 'Chaque erreur est une opportunité d\'apprendre. Réessayez ce challenge !';
        }
    }

    /**
     * Détermine le titre du header selon le pourcentage
     */
    private function getHeaderTitle(int $percentage): string
    {
        if ($percentage >= 80) {
            return 'Félicitations !';
        } elseif ($percentage >= 50) {
            return 'Bravo !';
        } elseif ($percentage >= 30) {
            return 'Challenge terminé';
        } else {
            return 'À améliorer';
        }
    }

    /**
     * Détermine le message du footer selon le pourcentage
     */
    private function getFooterMessage(int $percentage): string
    {
        if ($percentage >= 80) {
            return 'Continuez sur cette lancée !';
        } elseif ($percentage >= 50) {
            return 'Continuez à progresser !';
        } elseif ($percentage >= 30) {
            return 'La persévérance paie toujours !';
        } else {
            return 'Le succès est la somme de petits efforts répétés jour après jour.';
        }
    }

    /**
     * Détermine le dégradé de début selon le pourcentage
     */
    private function getGradientStart(int $percentage): string
    {
        if ($percentage >= 80) {
            return '#7fb77e';
        } elseif ($percentage >= 50) {
            return '#f3b562';
        } else {
            return '#f17b7b';
        }
    }

    /**
     * Détermine le dégradé de fin selon le pourcentage
     */
    private function getGradientEnd(int $percentage): string
    {
        if ($percentage >= 80) {
            return '#4a9b4a';
        } elseif ($percentage >= 50) {
            return '#f0a53b';
        } else {
            return '#e05a5a';
        }
    }

    /**
     * Détermine la couleur selon le pourcentage
     */
    private function getScoreColor(int $percentage): string
    {
        if ($percentage < 30) {
            return '#f17b7b'; // Rouge
        } elseif ($percentage < 50) {
            return '#f3b562'; // Orange
        } elseif ($percentage < 80) {
            return '#7fb77e'; // Vert clair
        } else {
            return '#4a9b4a'; // Vert foncé
        }
    }

    /**
     * Détermine le niveau selon le pourcentage
     */
    private function getLevelFromPercentage(int $percentage): string
    {
        if ($percentage >= 80) {
            return 'Expert';
        } elseif ($percentage >= 50) {
            return 'Intermédiaire';
        } else {
            return 'Débutant';
        }
    }
}