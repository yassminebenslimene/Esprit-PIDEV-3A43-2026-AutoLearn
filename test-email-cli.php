<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Créer le transport
$dsn = $_ENV['MAILER_DSN'];
echo "DSN utilisé : " . $dsn . "\n\n";

$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

// Créer l'email
$email = (new Email())
    ->from('autolearnplateforme@gmail.com')
    ->to('autolearnplateforme@gmail.com')
    ->subject('Test Email CLI - Autolearn')
    ->html('<h1>Test réussi depuis CLI !</h1><p>L\'intégration SendGrid fonctionne.</p>');

try {
    $mailer->send($email);
    echo "✅ Email envoyé avec succès !\n";
    echo "Vérifie ton email autolearnplateforme@gmail.com\n";
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
