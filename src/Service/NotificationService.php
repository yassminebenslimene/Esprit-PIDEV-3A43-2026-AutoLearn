<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Crée une notification interne dans la base de données
     */
    public function createInternalNotification(User $user, string $type, string $title, string $message): Notification
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTitle($title);
        $notification->setMessage($message);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->logger->info('Notification created', [
            'user_id' => $user->getId(),
            'type' => $type,
            'title' => $title
        ]);

        return $notification;
    }

    /**
     * Envoie un rappel d'inactivité
     */
    public function sendInactivityReminder(User $user, int $days): array
    {
        try {
            $title = "⏰ Rappel d'activité";
            $message = "Vous n'avez pas visité la plateforme depuis {$days} jours. Revenez pour continuer votre apprentissage !";
            
            $this->createInternalNotification($user, 'inactivity_reminder', $title, $message);
            
            return ['internal' => true, 'success' => true];
        } catch (\Exception $e) {
            $this->logger->error('Failed to send inactivity reminder', [
                'user_id' => $user->getId(),
                'error' => $e->getMessage()
            ]);
            return ['internal' => false, 'success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Envoie une notification générique
     */
    public function sendNotification(User $user, string $type, string $title, string $message, bool $sendSms = false): array
    {
        try {
            $this->createInternalNotification($user, $type, $title, $message);
            
            // TODO: Implémenter l'envoi SMS si nécessaire
            if ($sendSms) {
                $this->logger->info('SMS sending not implemented yet');
            }
            
            return ['internal' => true, 'success' => true];
        } catch (\Exception $e) {
            $this->logger->error('Failed to send notification', [
                'user_id' => $user->getId(),
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return ['internal' => false, 'success' => false, 'error' => $e->getMessage()];
        }
    }
}
