<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

#[Route('/backoffice/audit')]
class AuditController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'backoffice_audit_index')]
    public function index(): Response
    {
        $connection = $this->entityManager->getConnection();
        $revisions = [];
        
        try {
            // Check if tables exist
            $revisionsExists = $connection->executeQuery(
                "SELECT COUNT(*) FROM information_schema.tables 
                 WHERE table_schema = DATABASE() AND table_name = 'revisions'"
            )->fetchOne();
            
            $userAuditExists = $connection->executeQuery(
                "SELECT COUNT(*) FROM information_schema.tables 
                 WHERE table_schema = DATABASE() AND table_name = 'user_audit'"
            )->fetchOne();
            
            if ($revisionsExists && $userAuditExists) {
                $sql = "SELECT r.id, r.timestamp, r.username, 
                               COUNT(ua.userId) as changes_count
                        FROM revisions r
                        LEFT JOIN user_audit ua ON r.id = ua.rev
                        GROUP BY r.id, r.timestamp, r.username
                        HAVING changes_count > 0
                        ORDER BY r.timestamp DESC
                        LIMIT 100";
                
                $revisions = $connection->executeQuery($sql)->fetchAllAssociative();
            }
        } catch (\Exception $e) {
            $revisions = [];
        }

        return $this->render('backoffice/audit/index.html.twig', [
            'revisions' => $revisions,
        ]);
    }

    #[Route('/revision/{revisionId}', name: 'backoffice_audit_revision_details')]
    public function revisionDetails(int $revisionId): Response
    {
        $connection = $this->entityManager->getConnection();
        
        try {
            $revisionSql = "SELECT * FROM revisions WHERE id = ?";
            $revision = $connection->executeQuery($revisionSql, [$revisionId])->fetchAssociative();
            
            if (!$revision) {
                throw $this->createNotFoundException('Revision not found');
            }
            
            $changesSql = "SELECT * FROM user_audit WHERE rev = ?";
            $changes = $connection->executeQuery($changesSql, [$revisionId])->fetchAllAssociative();
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Audit data not available');
        }

        return $this->render('backoffice/audit/revision_details.html.twig', [
            'revision' => $revision,
            'changes' => $changes,
        ]);
    }

    #[Route('/user/{userId}', name: 'backoffice_audit_user_history')]
    public function userHistory(int $userId): Response
    {
        $connection = $this->entityManager->getConnection();
        
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        
        if (!$user->isEtudiant()) {
            throw $this->createAccessDeniedException('Audit history is only available for students');
        }
        
        $history = [];
        try {
            $sql = "SELECT ua.*, r.timestamp, r.username
                    FROM user_audit ua
                    JOIN revisions r ON ua.rev = r.id
                    WHERE ua.userId = ?
                    ORDER BY r.timestamp DESC";
            
            $history = $connection->executeQuery($sql, [$userId])->fetchAllAssociative();
        } catch (\Exception $e) {
            $history = [];
        }

        return $this->render('backoffice/audit/user_history.html.twig', [
            'user' => $user,
            'history' => $history,
        ]);
    }

    #[Route('/stats', name: 'backoffice_audit_stats')]
    public function stats(): Response
    {
        $connection = $this->entityManager->getConnection();
        
        $stats = [
            'total_revisions' => 0,
            'total_changes' => 0,
            'by_type' => [],
            'recent_activity' => [],
            'active_users' => []
        ];
        
        try {
            $stats['total_revisions'] = $connection->executeQuery(
                "SELECT COUNT(*) FROM revisions"
            )->fetchOne();
            
            $stats['total_changes'] = $connection->executeQuery(
                "SELECT COUNT(*) FROM user_audit"
            )->fetchOne();
            
            $stats['by_type'] = $connection->executeQuery(
                "SELECT revtype, COUNT(*) as count FROM user_audit GROUP BY revtype"
            )->fetchAllAssociative();
            
            $stats['recent_activity'] = $connection->executeQuery(
                "SELECT DATE(timestamp) as date, COUNT(*) as count 
                 FROM revisions 
                 WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY DATE(timestamp)
                 ORDER BY date DESC"
            )->fetchAllAssociative();
            
            $stats['active_users'] = $connection->executeQuery(
                "SELECT username, COUNT(*) as count 
                 FROM revisions 
                 WHERE username IS NOT NULL
                 GROUP BY username 
                 ORDER BY count DESC 
                 LIMIT 10"
            )->fetchAllAssociative();
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        return $this->render('backoffice/audit/stats.html.twig', [
            'stats' => $stats,
        ]);
    }
}
