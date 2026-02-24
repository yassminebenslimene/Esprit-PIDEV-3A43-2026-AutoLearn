<?php
/**
 * Test Simple Twilio
 * 
 * Instructions:
 * 1. Remplace les valeurs ci-dessous par tes vraies credentials
 * 2. Exécute: php public/test-twilio-simple.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

// ⚠️ REMPLACE CES VALEURS PAR TES VRAIES CREDENTIALS
$accountSid = 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // Ton Account SID
$authToken = 'your_auth_token_here';                 // Ton Auth Token
$twilioNumber = '+15551234567';                      // Ton numéro Twilio
$toNumber = '+216XXXXXXXX';                          // Ton numéro vérifié (Tunisie)

echo "🔍 Test de configuration Twilio...\n\n";

// Vérifier que les credentials sont remplis
if ($accountSid === 'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx') {
    echo "❌ ERREUR: Tu dois remplacer les valeurs par défaut!\n";
    echo "   Ouvre public/test-twilio-simple.php et mets tes vraies credentials.\n";
    exit(1);
}

try {
    echo "📡 Connexion à Twilio...\n";
    $client = new Client($accountSid, $authToken);
    
    echo "📱 Envoi du SMS de test...\n";
    $message = $client->messages->create(
        $toNumber,
        [
            'from' => $twilioNumber,
            'body' => '✅ Test Twilio depuis AutoLearn - Configuration OK!'
        ]
    );
    
    echo "\n✅ SMS ENVOYÉ AVEC SUCCÈS!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    echo "To: " . $message->to . "\n";
    echo "From: " . $message->from . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n📱 Vérifie ton téléphone, tu devrais recevoir le SMS!\n";
    
} catch (\Twilio\Exceptions\RestException $e) {
    echo "\n❌ ERREUR TWILIO:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Messages d'aide selon l'erreur
    if ($e->getCode() === 20003) {
        echo "💡 Solution: Vérifie que ton Account SID et Auth Token sont corrects.\n";
    } elseif ($e->getCode() === 21211) {
        echo "💡 Solution: Le numéro destinataire est invalide.\n";
        echo "   Format requis: +216XXXXXXXX (avec indicatif pays)\n";
    } elseif ($e->getCode() === 21608) {
        echo "💡 Solution: Le numéro n'est pas vérifié.\n";
        echo "   Va sur https://console.twilio.com et vérifie ton numéro.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERREUR GÉNÉRALE:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $e->getMessage() . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}
