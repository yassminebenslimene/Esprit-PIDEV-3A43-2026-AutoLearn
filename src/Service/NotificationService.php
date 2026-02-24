<?php
namespace App\Service;
use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
class NotificationService {
private $entityManager;
private $logger;
public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger) {
$this->entityManager = $entityManager;
$this->logger = $logger;
}
public function createInternalNotification(User $user, string $type, string $title, string $message): Notification {
$n = new Notification();
$n->setUser($user);
$n->setType($type);
$n->setTitle($title);
$n->setMessage($message);
$this->entityManager->persist($n);
$this->entityManager->flush();
return $n;
}
public function sendInactivityReminder(User $user, int $days): array {
try {
$this->createInternalNotification($user, "inactivity_reminder", "Rappel", "Message");
return ["internal" => true];
} catch (\Exception $e) { return ["internal" => false]; }
}
public function sendNotification(User $user, string $type, string $title, string $message, bool $sendSms = false): array {
try {
$this->createInternalNotification($user, $type, $title, $message);
return ["internal" => true];
} catch (\Exception $e) { return ["internal" => false]; }
}
}
