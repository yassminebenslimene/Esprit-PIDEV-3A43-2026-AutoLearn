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
        $studentRevisions = [];
        $contentRevisions = [];
        
        try {
            // Check if revisions table exists
            $revisionsExists = $connection->executeQuery(
                "SELECT COUNT(*) FROM information_schema.tables 
                 WHERE table_schema = DATABASE() AND table_name = 'revisions'"
            )->fetchOne();
            
            if ($revisionsExists) {
                // Query 1: Admin actions on STUDENTS only with action type detection
                $studentSql = "
                    SELECT 'student' as entity_type, r.id, r.timestamp, r.username, 
                           ua.userId as entity_id, ua.revtype, ua.nom, ua.prenom,
                           ua.isSuspended, ua.suspendedAt, ua.suspensionReason,
                           CASE 
                               WHEN ua.revtype = 'INS' THEN 'CREATE'
                               WHEN ua.revtype = 'DEL' THEN 'DELETE'
                               WHEN ua.isSuspended = 1 AND ua.suspendedAt IS NOT NULL THEN 'SUSPEND'
                               WHEN ua.isSuspended = 0 AND prev.isSuspended = 1 THEN 'REACTIVATE'
                               ELSE 'UPDATE'
                           END as action_type
                    FROM revisions r
                    LEFT JOIN user_audit ua ON r.id = ua.rev
                    LEFT JOIN user_audit prev ON prev.userId = ua.userId 
                        AND prev.rev = (
                            SELECT MAX(ua2.rev) 
                            FROM user_audit ua2 
                            WHERE ua2.userId = ua.userId AND ua2.rev < ua.rev
                        )
                    WHERE ua.userId IS NOT NULL AND ua.discr = 'etudiant'
                    ORDER BY r.timestamp DESC 
                    LIMIT 100
                ";
                
                $studentRevisions = $connection->executeQuery($studentSql)->fetchAllAssociative();
                
                // Query 2: Admin actions on CONTENT (courses, challenges, events, etc.)
                $contentSql = "
                    SELECT 'cours' as entity_type, r.id, r.timestamp, r.username,
                           ca.id as entity_id, ca.revtype, ca.titre as nom, NULL as prenom
                    FROM revisions r
                    LEFT JOIN cours_audit ca ON r.id = ca.rev
                    WHERE ca.id IS NOT NULL
                    
                    UNION ALL
                    
                    SELECT 'chapitre' as entity_type, r.id, r.timestamp, r.username,
                           ch.id as entity_id, ch.revtype, ch.titre as nom, NULL as prenom
                    FROM revisions r
                    LEFT JOIN chapitre_audit ch ON r.id = ch.rev
                    WHERE ch.id IS NOT NULL
                    
                    UNION ALL
                    
                    SELECT 'challenge' as entity_type, r.id, r.timestamp, r.username,
                           chal.id as entity_id, chal.revtype, chal.titre as nom, NULL as prenom
                    FROM revisions r
                    LEFT JOIN challenge_audit chal ON r.id = chal.rev
                    WHERE chal.id IS NOT NULL
                    
                    UNION ALL
                    
                    SELECT 'evenement' as entity_type, r.id, r.timestamp, r.username,
                           ev.id as entity_id, ev.revtype, ev.titre as nom, NULL as prenom
                    FROM revisions r
                    LEFT JOIN evenement_audit ev ON r.id = ev.rev
                    WHERE ev.id IS NOT NULL
                    
                    UNION ALL
                    
                    SELECT 'communaute' as entity_type, r.id, r.timestamp, r.username,
                           com.id as entity_id, com.revtype, com.nom as nom, NULL as prenom
                    FROM revisions r
                    LEFT JOIN communaute_audit com ON r.id = com.rev
                    WHERE com.id IS NOT NULL
                    
                    ORDER BY timestamp DESC 
                    LIMIT 100
                ";
                
                $contentRevisions = $connection->executeQuery($contentSql)->fetchAllAssociative();
            }
        } catch (\Exception $e) {
            $studentRevisions = [];
            $contentRevisions = [];
        }

        return $this->render('backoffice/audit/index.html.twig', [
            'studentRevisions' => $studentRevisions,
            'contentRevisions' => $contentRevisions,
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
            
            // Get admin info who performed the action
            $adminUser = null;
            if ($revision['username']) {
                $adminUser = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $revision['username']]);
            }
            
            // Get student info for each change
            foreach ($changes as &$change) {
                $student = $this->entityManager->getRepository(User::class)->find($change['userId']);
                $change['student'] = $student;
            }
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Audit data not available');
        }

        return $this->render('backoffice/audit/revision_details.html.twig', [
            'revision' => $revision,
            'changes' => $changes,
            'admin' => $adminUser,
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
            // Count ONLY admin revisions (filter by admin role)
            $stats['total_revisions'] = $connection->executeQuery(
                "SELECT COUNT(DISTINCT r.id) 
                 FROM revisions r
                 INNER JOIN user u ON u.email = r.username
                 WHERE u.role = 'ADMIN'"
            )->fetchOne();
            
            // Count ONLY admin changes on students
            $stats['total_changes'] = $connection->executeQuery(
                "SELECT COUNT(*) 
                 FROM user_audit ua
                 INNER JOIN revisions r ON ua.rev = r.id
                 INNER JOIN user u ON u.email = r.username
                 WHERE u.role = 'ADMIN'"
            )->fetchOne();
            
            // Count by action type - analyze the data to determine specific actions
            // Use self-join to compare with previous revision to detect REACTIVATE
            $stats['by_type'] = $connection->executeQuery(
                "SELECT 
                    CASE 
                        WHEN ua.revtype = 'INS' THEN 'CREATE'
                        WHEN ua.revtype = 'DEL' THEN 'DELETE'
                        WHEN ua.isSuspended = 1 AND ua.suspendedAt IS NOT NULL THEN 'SUSPEND'
                        WHEN ua.isSuspended = 0 AND prev.isSuspended = 1 THEN 'REACTIVATE'
                        ELSE 'UPDATE'
                    END as revtype,
                    COUNT(*) as count 
                 FROM user_audit ua
                 INNER JOIN revisions r ON ua.rev = r.id
                 INNER JOIN user u ON u.email = r.username
                 LEFT JOIN user_audit prev ON prev.userId = ua.userId 
                     AND prev.rev = (
                         SELECT MAX(ua2.rev) 
                         FROM user_audit ua2 
                         WHERE ua2.userId = ua.userId AND ua2.rev < ua.rev
                     )
                 WHERE u.role = 'ADMIN'
                 GROUP BY 1
                 ORDER BY count DESC"
            )->fetchAllAssociative();
            
            // Recent activity (last 7 days) - ONLY admin actions
            $stats['recent_activity'] = $connection->executeQuery(
                "SELECT DATE(r.timestamp) as date, COUNT(DISTINCT r.id) as count 
                 FROM revisions r
                 INNER JOIN user u ON u.email = r.username
                 WHERE u.role = 'ADMIN'
                 AND r.timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY DATE(r.timestamp)
                 ORDER BY date DESC"
            )->fetchAllAssociative();
            
            // Most active admins
            $stats['active_users'] = $connection->executeQuery(
                "SELECT r.username, COUNT(DISTINCT r.id) as count 
                 FROM revisions r
                 INNER JOIN user u ON u.email = r.username
                 WHERE u.role = 'ADMIN'
                 GROUP BY r.username 
                 ORDER BY count DESC 
                 LIMIT 10"
            )->fetchAllAssociative();
            
            // Get admin info for active users
            foreach ($stats['active_users'] as &$activeUser) {
                $admin = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $activeUser['username']]);
                $activeUser['admin'] = $admin;
            }
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        return $this->render('backoffice/audit/stats.html.twig', [
            'stats' => $stats,
        ]);
    }
}
