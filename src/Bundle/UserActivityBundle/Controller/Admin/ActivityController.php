<?php

namespace App\Bundle\UserActivityBundle\Controller\Admin;

use App\Bundle\UserActivityBundle\Repository\UserActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/backoffice/user-activity')]
#[IsGranted('ROLE_ADMIN')]
class ActivityController extends AbstractController
{
    #[Route('', name: 'admin_user_activity_index')]
    public function index(UserActivityRepository $repository): Response
    {
        $activities = $repository->findRecentActivities(100);
        
        return $this->render('@UserActivity/admin/index.html.twig', [
            'activities' => $activities,
        ]);
    }
    
    #[Route('/user/{id}', name: 'admin_user_activity_show')]
    public function showUserActivities(int $id, UserActivityRepository $repository): Response
    {
        $activities = $repository->findBy(['user' => $id], ['createdAt' => 'DESC'], 50);
        
        return $this->render('@UserActivity/admin/user_activities.html.twig', [
            'activities' => $activities,
            'userId' => $id,
        ]);
    }
}
