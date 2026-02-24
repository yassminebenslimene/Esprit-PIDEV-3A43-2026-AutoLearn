<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service responsable de l'envoi de notifications multi-canaux
 * 
 * Architecture modulaire :
 * - Gère les notifications internes (base de données)
 * - Gère les notifications externes (SMS via Twilio)
 * - Sépare la logique d'envoi de la logique métier
 */
class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TwilioSmsService $twilioService,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Envoie une notification de rappel d'inactivité (double canal)
     * 
     * @param User $user L'étudiant inactif
     * @param int $inactivityDays Nombre de jours d'inactivité
     * @return array Résultat de l'envoi ['internal' => bool, 'sms' => bool]
     */
    public function sendInactivityReminder(User $user, int $inactivityDays): array
    {
        $results = [
            'internal' => false,
            'sms' => false
        ];

        // 1️⃣ Notification interne (base de données)
        try {
            $this->createInternalNotification(
                $user,
                'inactivity_reminder',
                '⏰ Rappel d\'activité',
                sprintf(
                    'Bonjour %s, nous avons remarqué que vous n\'avez pas validé de chapitre depuis %d jours. ' .
                    'Continuez votre apprentissage pour progresser ! 🚀',
                    $user->getPrenom(),
                    $inactivityDays
                )
            );
            $results['internal'] = true;
            
            $this->logger->info('Notification interne envoyée', [
                'user_id' => $user->getId(),
                'type' => 'inactivity_reminder'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur notification interne', [
                'user_id' => $user->getId(),
                'error' => $e->getMessage()
            ]);
        }

        // 2️⃣ Notification externe (SMS via Twilio)
        if ($user->getPhoneNumber()) {
            try {
                $smsMessage = sprintf(
                    'Bonjour %s, vous n\'avez pas validé de chapitre depuis %d jours sur Autolearn. ' .
                    'Continuez votre apprentissage ! 🎓',
                    $user->getPrenom(),
                    $inactivityDays
                );

                $results['sms'] = $this->twilioService->sendSms(
                    $user->getPhoneNumber(),
                    $smsMessage
                );

                $this->logger->info('SMS envoyé', [
                    'user_id' => $user->getId(),
                    'phone' => $user->getPhoneNumber()
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Erreur envoi SMS', [
                    'user_id' => $user->getId(),
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            $this->logger->warning('Pas de numéro de téléphone', [
                'user_id' => $user->getId()
            ]);
        }

        return $results;
    }

    /**
     * Crée une notification interne dans la base de données
     * 
     * @param User $user Destinataire
     * @param string $type Type de notification
     * @param string $title Titre
     * @param string $message Message
     * @return Notification
     */
    public function createInternalNotification(
        User $user,
        string $type,
        string $title,
        string $message
    ): Notification {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTitle($title);
        $notification->setMessage($message);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    /**
     * Envoie une notification générique multi-canaux
     * 
     * @param User $user Destinataire
     * @param string $type Type de notification
     * @param string $title Titre
     * @param string $message Message
     * @param bool $sendSms Envoyer aussi par SMS
     * @return array Résultat de l'envoi
     */
    public function sendNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        bool $sendSms = false
    ): array {
        $results = [
            'internal' => false,
            'sms' => false
        ];

        // Notification interne
        try {
            $this->createInternalNotification($user, $type, $title, $message);
            $results['internal'] = true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur notification interne', [
                'user_id' => $user->getId(),
                'error' => $e->getMessage()
            ]);
        }

        // SMS optionnel
        if ($sendSms && $user->getPhoneNumber()) {
            try {
                $results['sms'] = $this->twilioService->sendSms(
                    $user->getPhoneNumber(),
                    $message
                );
            } catch (\Exception $e) {
                $this->logger->error('Erreur envoi SMS', [
                    'user_id' => $user->getId(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }
}
