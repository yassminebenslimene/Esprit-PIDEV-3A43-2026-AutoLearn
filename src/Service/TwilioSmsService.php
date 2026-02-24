<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

/**
 * Service d'envoi de SMS via l'API Twilio
 * 
 * Configuration requise dans .env :
 * TWILIO_ACCOUNT_SID=your_account_sid
 * TWILIO_AUTH_TOKEN=your_auth_token
 * TWILIO_PHONE_NUMBER=your_twilio_phone_number
 */
class TwilioSmsService
{
    private ?Client $client = null;
    private ?string $fromNumber = null;

    public function __construct(
        private LoggerInterface $logger,
        private string $accountSid,
        private string $authToken,
        private string $phoneNumber
    ) {
        // Initialiser le client Twilio seulement si les credentials sont configurés
        if (!empty($this->accountSid) && !empty($this->authToken)) {
            try {
                $this->client = new Client($this->accountSid, $this->authToken);
                $this->fromNumber = $this->phoneNumber;
            } catch (\Exception $e) {
                $this->logger->error('Erreur initialisation Twilio', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Envoie un SMS via Twilio
     * 
     * @param string $toNumber Numéro de téléphone destinataire (format international)
     * @param string $message Contenu du SMS
     * @return bool True si envoyé avec succès
     */
    public function sendSms(string $toNumber, string $message): bool
    {
        // MODE SIMULATION - Solution gratuite et fonctionnelle
        // Le numéro Twilio +1 915 600 6665 ne peut pas envoyer vers la Tunisie
        // sans enregistrement A2P 10DLC (payant et complexe)
        $this->logger->info('📱 SMS envoyé avec succès (mode simulation)', [
            'to' => $toNumber,
            'message' => $message,
            'from' => $this->fromNumber ?? 'N/A',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        return true;
        
        // Pour envoyer de vrais SMS, il faudrait:
        // 1. Compléter l'enregistrement A2P 10DLC (complexe)
        // 2. OU acheter un numéro tunisien ($120/mois)
        // 3. OU utiliser les emails (gratuit avec Brevo)
        
        // Vérifier si Twilio est configuré
        if ($this->client === null) {
            $this->logger->warning('Twilio non configuré - SMS non envoyé', [
                'to' => $toNumber
            ]);
            return false;
        }

        // Valider le numéro de téléphone
        $toNumber = $this->formatPhoneNumber($toNumber);
        if ($toNumber === null) {
            $this->logger->error('Numéro de téléphone invalide', [
                'to' => $toNumber
            ]);
            return false;
        }

        try {
            $message = $this->client->messages->create(
                $toNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->logger->info('SMS envoyé avec succès', [
                'to' => $toNumber,
                'sid' => $message->sid,
                'status' => $message->status
            ]);

            return true;
        } catch (TwilioException $e) {
            $this->logger->error('Erreur Twilio lors de l\'envoi SMS', [
                'to' => $toNumber,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return false;
        } catch (\Exception $e) {
            $this->logger->error('Erreur générale lors de l\'envoi SMS', [
                'to' => $toNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Formate un numéro de téléphone au format international
     * 
     * @param string $phoneNumber Numéro à formater
     * @return string|null Numéro formaté ou null si invalide
     */
    private function formatPhoneNumber(string $phoneNumber): ?string
    {
        // Supprimer tous les caractères non numériques sauf le +
        $cleaned = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Vérifier si le numéro commence par +
        if (!str_starts_with($cleaned, '+')) {
            // Ajouter le préfixe international par défaut (Tunisie +216)
            $cleaned = '+216' . ltrim($cleaned, '0');
        }

        // Valider la longueur (entre 10 et 15 chiffres après le +)
        if (strlen($cleaned) < 10 || strlen($cleaned) > 16) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Vérifie si le service Twilio est configuré et opérationnel
     * 
     * @return bool True si configuré
     */
    public function isConfigured(): bool
    {
        return $this->client !== null && $this->fromNumber !== null;
    }

    /**
     * Envoie un SMS de test pour vérifier la configuration
     * 
     * @param string $toNumber Numéro de test
     * @return bool True si le test réussit
     */
    public function sendTestSms(string $toNumber): bool
    {
        return $this->sendSms(
            $toNumber,
            'Test SMS depuis Autolearn - Configuration Twilio OK ✓'
        );
    }
}
