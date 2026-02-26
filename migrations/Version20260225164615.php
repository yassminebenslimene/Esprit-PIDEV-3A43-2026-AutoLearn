<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour créer les tables du bundle EntityAudit (revisions et user_audit)
 * Avec TOUTES les colonnes nécessaires pour éviter les erreurs
 */
final class Version20260225164615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création complète des tables revisions et user_audit avec toutes les colonnes nécessaires pour le bundle EntityAudit';
    }

    public function up(Schema $schema): void
    {
        // Créer la table user_audit pour le bundle EntityAudit si elle n'existe pas
        // Avec TOUTES les colonnes possibles pour éviter les erreurs
        $this->addSql('CREATE TABLE IF NOT EXISTS user_audit (
            id INT AUTO_INCREMENT NOT NULL,
            rev INT DEFAULT NULL,
            revtype VARCHAR(4) NOT NULL,
            userId INT DEFAULT NULL,
            type VARCHAR(10) NOT NULL,
            object_id VARCHAR(255) NOT NULL,
            discriminator VARCHAR(255) DEFAULT NULL,
            transaction_hash VARCHAR(40) DEFAULT NULL,
            diffs LONGTEXT DEFAULT NULL,
            blame_id VARCHAR(255) DEFAULT NULL,
            blame_user VARCHAR(255) DEFAULT NULL,
            blame_user_fqdn VARCHAR(255) DEFAULT NULL,
            blame_user_firewall VARCHAR(100) DEFAULT NULL,
            ip VARCHAR(45) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            INDEX blame_id_idx (blame_id),
            INDEX object_id_idx (object_id),
            INDEX created_at_idx (created_at),
            INDEX discriminator_idx (discriminator),
            INDEX transaction_hash_idx (transaction_hash),
            INDEX type_idx (type),
            INDEX rev_idx (rev),
            INDEX userId_idx (userId),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        // Créer la table revisions pour le bundle EntityAudit
        $this->addSql('CREATE TABLE IF NOT EXISTS revisions (
            id INT AUTO_INCREMENT NOT NULL,
            timestamp DATETIME NOT NULL,
            username VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les tables en rollback
        $this->addSql('DROP TABLE IF EXISTS user_audit');
        $this->addSql('DROP TABLE IF EXISTS revisions');
    }
}
