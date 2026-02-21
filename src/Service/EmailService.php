<?php
// src/Service/EmailService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendChallengeReceipt(string $to, string $challengeTitle, int $score, int $totalPoints, \DateTimeImmutable $completedAt): void
    {
        $percentage = round(($score / $totalPoints) * 100);
        
        $email = (new Email())
            ->from('noreply@scholar.com')
            ->to($to)
            ->subject('Récapitulatif de votre challenge - ' . $challengeTitle)
            ->html($this->getHtmlContent($challengeTitle, $score, $totalPoints, $percentage, $completedAt));

        $this->mailer->send($email);
    }

    private function getHtmlContent(string $challengeTitle, int $score, int $totalPoints, int $percentage, \DateTimeImmutable $completedAt): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(105deg, #7fb77e 0%, #4a9b4a 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .score { font-size: 48px; font-weight: bold; color: #7fb77e; text-align: center; margin: 20px 0; }
                .details { margin: 20px 0; }
                .detail-item { margin: 10px 0; padding: 10px; background: white; border-radius: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #7a7a7a; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Félicitations !</h1>
                </div>
                <div class="content">
                    <p>Vous avez complété le challenge <strong>"' . $challengeTitle . '"</strong> avec succès.</p>
                    
                    <div class="score">
                        ' . $score . '/' . $totalPoints . '
                    </div>
                    
                    <div class="details">
                        <div class="detail-item">
                            <strong>Pourcentage de réussite :</strong> ' . $percentage . '%
                        </div>
                        <div class="detail-item">
                            <strong>Date de complétion :</strong> ' . $completedAt->format('d/m/Y H:i') . '
                        </div>
                    </div>
                    
                    <p>Continuez à relever de nouveaux défis !</p>
                </div>
                <div class="footer">
                    <p>© ' . date('Y') . ' Scholar. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
}