<?php

namespace App\Bundle\UserActivityBundle\Service;

use App\Bundle\UserActivityBundle\Entity\UserActivity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class ActivityLogger
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private Security $security
    ) {}

    /**
     * Log an activity for a specific user or the current authenticated user
     */
    public function log(string $action, ?array $metadata = null, bool $success = true, ?string $errorMessage = null, ?User $targetUser = null): void
    {
        // Use target user if provided, otherwise use current authenticated user
        $user = $targetUser ?? $this->security->getUser();
        
        if (!$user) {
            return; // Don't log if no user is available
        }

        $request = $this->requestStack->getCurrentRequest();
        
        $activity = new UserActivity();
        $activity->setUser($user);
        $activity->setAction($action);
        $activity->setSuccess($success);
        $activity->setErrorMessage($errorMessage);
        
        if ($request) {
            $activity->setIpAddress($request->getClientIp());
            $activity->setUserAgent($request->headers->get('User-Agent'));
            
            // Add request details to metadata
            $enrichedMetadata = $metadata ?? [];
            $enrichedMetadata['request_method'] = $request->getMethod();
            $enrichedMetadata['request_uri'] = $request->getRequestUri();
            $enrichedMetadata['referer'] = $request->headers->get('referer');
            
            // Parse user agent for better details
            $userAgent = $request->headers->get('User-Agent');
            $enrichedMetadata['browser'] = $this->parseBrowser($userAgent);
            $enrichedMetadata['platform'] = $this->parsePlatform($userAgent);
            
            $activity->setMetadata($enrichedMetadata);
        } elseif ($metadata) {
            $activity->setMetadata($metadata);
        }

        $this->em->persist($activity);
        $this->em->flush();
    }

    /**
     * Log a login activity for a specific user
     */
    public function logLogin(User $user, bool $success = true, ?string $errorMessage = null): void
    {
        $metadata = [
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail(),
            'user_name' => $user->getPrenom() . ' ' . $user->getNom(),
            'user_role' => $user->getRole(),
            'login_time' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($user->getIsSuspended()) {
            $metadata['suspension_status'] = 'suspended';
            $metadata['suspension_reason'] = $user->getSuspensionReason();
            $metadata['suspended_at'] = $user->getSuspendedAt()?->format('Y-m-d H:i:s');
        }
        
        $this->log('user.login', $metadata, $success, $errorMessage, $user);
    }

    /**
     * Log a logout activity
     */
    public function logLogout(): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $metadata = [
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
                'logout_time' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];
            $this->log('user.logout', $metadata);
        }
    }

    /**
     * Log a user creation activity
     */
    public function logCreate(User $createdUser): void
    {
        $admin = $this->security->getUser();
        
        $metadata = [
            'created_user_id' => $createdUser->getId(),
            'created_user_email' => $createdUser->getEmail(),
            'created_user_name' => $createdUser->getPrenom() . ' ' . $createdUser->getNom(),
            'created_user_role' => $createdUser->getRole(),
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($admin instanceof User) {
            $metadata['created_by_id'] = $admin->getId();
            $metadata['created_by_email'] = $admin->getEmail();
            $metadata['created_by_name'] = $admin->getPrenom() . ' ' . $admin->getNom();
            $metadata['created_by_role'] = $admin->getRole();
        }
        
        if (method_exists($createdUser, 'getNiveau')) {
            $metadata['niveau'] = $createdUser->getNiveau();
        }
        
        $this->log('user.created', $metadata, true, null, $createdUser);
    }

    /**
     * Log a user update activity
     */
    public function logUpdate(User $updatedUser, ?array $changes = null): void
    {
        $admin = $this->security->getUser();
        
        $metadata = [
            'updated_user_id' => $updatedUser->getId(),
            'updated_user_email' => $updatedUser->getEmail(),
            'updated_user_name' => $updatedUser->getPrenom() . ' ' . $updatedUser->getNom(),
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($admin instanceof User) {
            $metadata['updated_by_id'] = $admin->getId();
            $metadata['updated_by_email'] = $admin->getEmail();
            $metadata['updated_by_name'] = $admin->getPrenom() . ' ' . $admin->getNom();
            $metadata['updated_by_role'] = $admin->getRole();
        }
        
        if ($changes !== null && $changes !== []) {
            $metadata['changes'] = $changes;
            $metadata['fields_changed'] = array_keys($changes);
            $metadata['changes_count'] = count($changes);
        }
        
        $this->log('user.updated', $metadata, true, null, $updatedUser);
    }

    /**
     * Log a user deletion activity
     */
    public function logDelete(User $deletedUser): void
    {
        $admin = $this->security->getUser();
        
        $metadata = [
            'deleted_user_id' => $deletedUser->getId(),
            'deleted_user_email' => $deletedUser->getEmail(),
            'deleted_user_name' => $deletedUser->getPrenom() . ' ' . $deletedUser->getNom(),
            'deleted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($admin instanceof User) {
            $metadata['deleted_by_id'] = $admin->getId();
            $metadata['deleted_by_email'] = $admin->getEmail();
            $metadata['deleted_by_name'] = $admin->getPrenom() . ' ' . $admin->getNom();
        }
        
        $this->log('user.deleted', $metadata, true, null, $deletedUser);
    }

    /**
     * Log a user profile view activity
     */
    public function logView(User $viewedUser): void
    {
        $admin = $this->security->getUser();
        
        $metadata = [
            'viewed_user_id' => $viewedUser->getId(),
            'viewed_user_email' => $viewedUser->getEmail(),
            'viewed_user_name' => $viewedUser->getPrenom() . ' ' . $viewedUser->getNom(),
            'viewed_user_role' => $viewedUser->getRole(),
            'viewed_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($admin instanceof User) {
            $metadata['viewed_by_id'] = $admin->getId();
            $metadata['viewed_by_email'] = $admin->getEmail();
            $metadata['viewed_by_name'] = $admin->getPrenom() . ' ' . $admin->getNom();
            $metadata['viewed_by_role'] = $admin->getRole();
        }
        
        if ($viewedUser->getIsSuspended()) {
            $metadata['user_status'] = 'suspended';
            $metadata['suspension_reason'] = $viewedUser->getSuspensionReason();
        } else {
            $metadata['user_status'] = 'active';
        }
        
        $this->log('user.viewed', $metadata, true, null, $viewedUser);
    }

    /**
     * Log a user suspension activity
     */
    public function logSuspend(User $suspendedUser, string $reason): void
    {
        $admin = $this->security->getUser();
        
        $metadata = [
            'suspended_user_id' => $suspendedUser->getId(),
            'suspended_user_email' => $suspendedUser->getEmail(),
            'suspended_user_name' => $suspendedUser->getPrenom() . ' ' . $suspendedUser->getNom(),
            'suspension_reason' => $reason,
            'suspended_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($admin instanceof User) {
            $metadata['suspended_by_id'] = $admin->getId();
            $metadata['suspended_by_email'] = $admin->getEmail();
            $metadata['suspended_by_name'] = $admin->getPrenom() . ' ' . $admin->getNom();
            $metadata['suspended_by_role'] = $admin->getRole();
        }
        
        // Add last login info if available
        if ($suspendedUser->getLastLoginAt()) {
            $metadata['last_login'] = $suspendedUser->getLastLoginAt()->format('Y-m-d H:i:s');
            $daysSinceLogin = (new \DateTime())->diff($suspendedUser->getLastLoginAt())->days;
            $metadata['days_since_last_login'] = $daysSinceLogin;
        }
        
        $this->log('user.suspended', $metadata, true, null, $suspendedUser);
    }

    /**
     * Log a user reactivation activity
     */
    public function logReactivate(User $reactivatedUser): void
    {
        $admin = $this->security->getUser();
        
        $metadata = [
            'reactivated_user_id' => $reactivatedUser->getId(),
            'reactivated_user_email' => $reactivatedUser->getEmail(),
            'reactivated_user_name' => $reactivatedUser->getPrenom() . ' ' . $reactivatedUser->getNom(),
            'reactivated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
        
        if ($admin instanceof User) {
            $metadata['reactivated_by_id'] = $admin->getId();
            $metadata['reactivated_by_email'] = $admin->getEmail();
            $metadata['reactivated_by_name'] = $admin->getPrenom() . ' ' . $admin->getNom();
            $metadata['reactivated_by_role'] = $admin->getRole();
        }
        
        // Add suspension duration if available
        if ($reactivatedUser->getSuspendedAt()) {
            $suspensionDuration = (new \DateTime())->diff($reactivatedUser->getSuspendedAt());
            $metadata['suspension_duration_days'] = $suspensionDuration->days;
            $metadata['suspension_duration_hours'] = $suspensionDuration->h + ($suspensionDuration->days * 24);
            $metadata['was_suspended_at'] = $reactivatedUser->getSuspendedAt()->format('Y-m-d H:i:s');
            $metadata['previous_suspension_reason'] = $reactivatedUser->getSuspensionReason();
        }
        
        $this->log('user.reactivated', $metadata, true, null, $reactivatedUser);
    }
    
    /**
     * Parse browser from user agent
     */
    private function parseBrowser(?string $userAgent): string
    {
        if (!$userAgent) return 'Unknown';
        
        if (preg_match('/Edge/i', $userAgent)) return 'Microsoft Edge';
        if (preg_match('/Chrome/i', $userAgent)) return 'Google Chrome';
        if (preg_match('/Firefox/i', $userAgent)) return 'Mozilla Firefox';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Opera|OPR/i', $userAgent)) return 'Opera';
        if (preg_match('/MSIE|Trident/i', $userAgent)) return 'Internet Explorer';
        
        return 'Unknown Browser';
    }
    
    /**
     * Parse platform from user agent
     */
    private function parsePlatform(?string $userAgent): string
    {
        if (!$userAgent) return 'Unknown';
        
        if (preg_match('/Windows NT 10/i', $userAgent)) return 'Windows 10/11';
        if (preg_match('/Windows NT 6.3/i', $userAgent)) return 'Windows 8.1';
        if (preg_match('/Windows NT 6.2/i', $userAgent)) return 'Windows 8';
        if (preg_match('/Windows NT 6.1/i', $userAgent)) return 'Windows 7';
        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) return 'macOS';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) return 'iOS';
        
        return 'Unknown Platform';
    }
}
