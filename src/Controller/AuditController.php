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
        
        // Direct simple test
        $testQuery = "SELECT COUNT(*) as count FROM user_audit WHERE discr = :discr";
        $testResult = $connection->executeQuery($testQuery, ['discr' => 'etudiant'])->fetchAssociative();
        
        $studentRevisions = [];
        $contentRevisions = [];
        
        // If we have data, try the full query
        if ($testResult['count'] > 0) {
            try {
                $studentSql = "
                    SELECT 
                        'student' as entity_type, 
                        r.id, 
                        r.timestamp, 
                        r.username, 
                        ua.userId as entity_id, 
                        ua.revtype, 
                        ua.nom, 
                        ua.prenom,
                        ua.isSuspended, 
                        ua.suspendedAt, 
                        ua.suspensionReason,
                        prev.isSuspended as prev_isSuspended,
                        CASE 
                            WHEN ua.revtype = 'INS' THEN 'CREATE'
                            WHEN ua.revtype = 'DEL' THEN 'DELETE'
                            WHEN ua.revtype = 'UPD' AND ua.isSuspended = 1 AND (prev.isSuspended IS NULL OR prev.isSuspended = 0) THEN 'SUSPEND'
                            WHEN ua.revtype = 'UPD' AND ua.isSuspended = 0 AND prev.isSuspended = 1 THEN 'REACTIVATE'
                            WHEN ua.revtype = 'UPD' THEN 'UPDATE'
                            ELSE 'UPDATE'
                        END as action_type
                    FROM revisions r
                    INNER JOIN user_audit ua ON r.id = ua.rev
                    LEFT JOIN user_audit prev ON prev.userId = ua.userId 
                        AND prev.rev = (
                            SELECT MAX(ua2.rev) 
                            FROM user_audit ua2 
                            WHERE ua2.userId = ua.userId AND ua2.rev < ua.rev
                        )
                    WHERE ua.userId IS NOT NULL AND ua.discr = :discr
                    ORDER BY r.timestamp DESC 
                    LIMIT 100
                ";
                
                $studentRevisions = $connection->executeQuery($studentSql, ['discr' => 'etudiant'])->fetchAllAssociative();
            } catch (\Exception $e) {
                // Store error for display
                $studentRevisions = [];
            }
        }
        
        $contentRevisions = [];
        $contentError = null;
        
        try {
            // Check which audit tables exist
            $tables = $connection->executeQuery(
                "SELECT TABLE_NAME FROM information_schema.TABLES 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME LIKE '%_audit' 
                 AND TABLE_NAME != 'user_audit'"
            )->fetchAllAssociative();
            
            // Query each table separately to avoid collation issues
            foreach ($tables as $table) {
                $tableName = $table['TABLE_NAME'];
                
                // Determine the display name column based on table
                $nameColumn = 'titre'; // Default for most tables
                if ($tableName === 'communaute_audit' || $tableName === 'equipe_audit') {
                    $nameColumn = 'nom';
                } elseif ($tableName === 'commentaire_audit') {
                    $nameColumn = 'contenu';
                } elseif ($tableName === 'exercice_audit') {
                    $nameColumn = 'question';
                }
                
                // Extract entity type from table name (remove _audit suffix)
                $entityType = str_replace('_audit', '', $tableName);
                
                try {
                    $sql = "
                        SELECT '$entityType' as entity_type, r.id, r.timestamp, r.username,
                               t.id as entity_id, t.revtype, t.$nameColumn as nom
                        FROM revisions r
                        INNER JOIN $tableName t ON r.id = t.rev
                        WHERE t.id IS NOT NULL
                        ORDER BY r.timestamp DESC
                        LIMIT 100
                    ";
                    
                    $results = $connection->executeQuery($sql)->fetchAllAssociative();
                    $contentRevisions = array_merge($contentRevisions, $results);
                } catch (\Exception $e) {
                    // Skip tables with errors
                    continue;
                }
            }
            
            // Sort all results by timestamp
            usort($contentRevisions, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            // Limit to 100 most recent
            $contentRevisions = array_slice($contentRevisions, 0, 100);
            
        } catch (\Exception $e) {
            // Store error for debugging
            $contentError = $e->getMessage();
        }

        return $this->render('backoffice/audit/index.html.twig', [
            'studentRevisions' => $studentRevisions,
            'contentRevisions' => $contentRevisions,
            'testCount' => $testResult['count'] ?? 0,
            'contentError' => $contentError ?? null,
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
            'active_admins' => [],
            'active_students' => []
        ];
        
        try {
            // Count all revisions
            $stats['total_revisions'] = $connection->executeQuery(
                "SELECT COUNT(*) FROM revisions"
            )->fetchOne();
            
            // Count all changes across all audit tables
            $stats['total_changes'] = $connection->executeQuery(
                "SELECT (
                    (SELECT COUNT(*) FROM user_audit) +
                    (SELECT COUNT(*) FROM cours_audit)
                ) as total"
            )->fetchOne();
            
            $stats['by_type'] = $connection->executeQuery(
                "SELECT 
                    CASE 
                        WHEN ua.revtype = 'INS' THEN 'CREATE'
                        WHEN ua.revtype = 'DEL' THEN 'DELETE'
                        WHEN ua.revtype = 'UPD' AND ua.isSuspended = 1 AND (prev.isSuspended IS NULL OR prev.isSuspended = 0) THEN 'SUSPEND'
                        WHEN ua.revtype = 'UPD' AND ua.isSuspended = 0 AND prev.isSuspended = 1 THEN 'REACTIVATE'
                        WHEN ua.revtype = 'UPD' THEN 'UPDATE'
                        ELSE 'UPDATE'
                    END as revtype,
                    COUNT(*) as count 
                 FROM user_audit ua
                 LEFT JOIN user_audit prev ON prev.userId = ua.userId 
                     AND prev.rev = (
                         SELECT MAX(ua2.rev) 
                         FROM user_audit ua2 
                         WHERE ua2.userId = ua.userId AND ua2.rev < ua.rev
                     )
                 WHERE ua.discr = :discr
                 GROUP BY 1
                 HAVING count > 0
                 ORDER BY count DESC"
            , ['discr' => 'etudiant'])->fetchAllAssociative();
            
            $stats['recent_activity'] = $connection->executeQuery(
                "SELECT DATE(r.timestamp) as date, COUNT(*) as count 
                 FROM revisions r
                 WHERE r.timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 GROUP BY DATE(r.timestamp)
                 ORDER BY date DESC"
            )->fetchAllAssociative();
            
            // Most active admins
            $stats['active_admins'] = $connection->executeQuery(
                "SELECT r.username, COUNT(*) as count 
                 FROM revisions r
                 INNER JOIN user u ON u.email = r.username
                 WHERE r.username IS NOT NULL AND u.role = :role
                 GROUP BY r.username 
                 ORDER BY count DESC 
                 LIMIT 10"
            , ['role' => 'ADMIN'])->fetchAllAssociative();
            
            // Get admin info for active admins
            foreach ($stats['active_admins'] as &$activeAdmin) {
                $admin = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $activeAdmin['username']]);
                $activeAdmin['admin'] = $admin;
            }
            
            // Most active students (students who were modified the most)
            $stats['active_students'] = $connection->executeQuery(
                "SELECT ua.userId, ua.nom, ua.prenom, COUNT(*) as count 
                 FROM user_audit ua
                 WHERE ua.discr = :discr
                 GROUP BY ua.userId, ua.nom, ua.prenom
                 ORDER BY count DESC 
                 LIMIT 10"
            , ['discr' => 'etudiant'])->fetchAllAssociative();
            
            // Get student info for active students
            foreach ($stats['active_students'] as &$activeStudent) {
                $student = $this->entityManager->getRepository(User::class)
                    ->find($activeStudent['userId']);
                $activeStudent['student'] = $student;
            }
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        return $this->render('backoffice/audit/stats.html.twig', [
            'stats' => $stats,
        ]);
    }
}
