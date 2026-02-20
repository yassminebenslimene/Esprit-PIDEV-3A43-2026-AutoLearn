<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestEmailController extends AbstractController
{
    #[Route('/test/email', name: 'test_email')]
    public function testEmail(EmailService $emailService): Response
    {
        try {
            // Envoi de l'email de test à l'adresse de la plateforme
            $emailService->sendTestEmail('autolearnplateforme@gmail.com');
            
            return new Response(
                '<h1>✅ Email envoyé avec succès !</h1>' .
                '<p>Vérifie ta boîte email <strong>autolearnplateforme@gmail.com</strong> (et spam si besoin).</p>' .
                '<a href="/">Retour à l\'accueil</a>'
            );
        } catch (\Exception $e) {
            return new Response(
                '<h1>❌ Erreur lors de l\'envoi</h1>' .
                '<p>Erreur : ' . $e->getMessage() . '</p>' .
                '<a href="/">Retour à l\'accueil</a>',
                500
            );
        }
    }
    
    #[Route('/test/certificate', name: 'test_certificate')]
    public function testCertificate(EmailService $emailService): Response
    {
        try {
            // Envoyer un certificat de test
            $emailService->sendCertificate(
                'autolearnplateforme@gmail.com',
                'Amira',
                'Test',
                'Hackathon Innovation 2026',
                'Hackathon',
                new \DateTime('2026-02-10')
            );
            
            return new Response(
                '<h1>✅ Certificat envoyé avec succès !</h1>' .
                '<p>Vérifie ta boîte email <strong>autolearnplateforme@gmail.com</strong></p>' .
                '<p>Le certificat PDF est en pièce jointe.</p>' .
                '<a href="/">Retour à l\'accueil</a>'
            );
        } catch (\Exception $e) {
            return new Response(
                '<h1>❌ Erreur lors de l\'envoi</h1>' .
                '<p>Erreur : ' . $e->getMessage() . '</p>' .
                '<a href="/">Retour à l\'accueil</a>',
                500
            );
        }
    }
}
