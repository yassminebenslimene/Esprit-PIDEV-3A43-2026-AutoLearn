<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_ETUDIANT')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Liste toutes les notifications de l'utilisateur connecté
     */
    #[Route('/', name: 'app_notifications_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        
        $notifications = $this->notificationRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        $unreadCount = $this->notificationRepository->count([
            'user' => $user,
            'isRead' => false
        ]);

        return $this->render('frontoffice/notifications/index.html.twig', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    #[Route('/{id}/mark-read', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification): Response
    {
        // Vérifier que la notification appartient à l'utilisateur connecté
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$notification->getIsRead()) {
            $notification->markAsRead();
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_notifications_index');
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    #[Route('/mark-all-read', name: 'app_notifications_mark_all_read', methods: ['POST'])]
    public function markAllAsRead(): Response
    {
        $user = $this->getUser();
        
        $notifications = $this->notificationRepository->findBy([
            'user' => $user,
            'isRead' => false
        ]);

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Toutes les notifications ont été marquées comme lues');
        return $this->redirectToRoute('app_notifications_index');
    }

    /**
     * Supprimer une notification
     */
    #[Route('/{id}/delete', name: 'app_notifications_delete', methods: ['POST'])]
    public function delete(Notification $notification): Response
    {
        // Vérifier que la notification appartient à l'utilisateur connecté
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        $this->addFlash('success', 'Notification supprimée');
        return $this->redirectToRoute('app_notifications_index');
    }

    /**
     * API: Récupérer le nombre de notifications non lues
     */
    #[Route('/api/unread-count', name: 'app_notifications_unread_count', methods: ['GET'])]
    public function getUnreadCount(): JsonResponse
    {
        $user = $this->getUser();
        
        $count = $this->notificationRepository->count([
            'user' => $user,
            'isRead' => false
        ]);

        return new JsonResponse(['count' => $count]);
    }

    /**
     * API: Récupérer les dernières notifications
     */
    #[Route('/api/recent', name: 'app_notifications_recent', methods: ['GET'])]
    public function getRecent(): JsonResponse
    {
        $user = $this->getUser();
        
        $notifications = $this->notificationRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC'],
            5 // Limiter à 5 notifications
        );

        $data = array_map(function(Notification $notification) {
            return [
                'id' => $notification->getId(),
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'isRead' => $notification->getIsRead(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }, $notifications);

        return new JsonResponse($data);
    }
}
