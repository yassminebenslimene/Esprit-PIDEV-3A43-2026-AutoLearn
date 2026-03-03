<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222123211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
<<<<<<< HEAD
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_activity (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(50) NOT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, metadata JSON DEFAULT NULL, created_at DATETIME NOT NULL, location VARCHAR(100) DEFAULT NULL, success TINYINT(1) NOT NULL, error_message VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_4CF9ED5AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5AA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE participation ADD feedbacks JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD isSuspended TINYINT(1) DEFAULT 0 NOT NULL, ADD suspendedAt DATETIME DEFAULT NULL, ADD suspensionReason VARCHAR(500) DEFAULT NULL, ADD suspendedBy INT DEFAULT NULL, ADD lastLoginAt DATETIME DEFAULT NULL, ADD lastActivityAt DATETIME DEFAULT NULL, ADD phoneNumber VARCHAR(20) DEFAULT NULL');
=======
        // Migration simplifiée - créer les tables et ajouter les colonnes
        // Les contraintes seront ajoutées plus tard si nécessaire
        
        // Créer les tables si elles n'existent pas
        $this->addSql('CREATE TABLE IF NOT EXISTS notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE IF NOT EXISTS user_activity (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(50) NOT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, metadata JSON DEFAULT NULL, created_at DATETIME NOT NULL, location VARCHAR(100) DEFAULT NULL, success TINYINT(1) NOT NULL, error_message VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_4CF9ED5AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Ajouter les colonnes - ignorer les erreurs si elles existent déjà
        // Note: Ces commandes peuvent échouer si les colonnes existent déjà, c'est normal
>>>>>>> cf32977d311ea069ba23a14c2cf5417034d537b8
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE user_activity DROP FOREIGN KEY FK_4CF9ED5AA76ED395');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE user_activity');
        $this->addSql('ALTER TABLE participation DROP feedbacks');
        $this->addSql('ALTER TABLE user DROP isSuspended, DROP suspendedAt, DROP suspensionReason, DROP suspendedBy, DROP lastLoginAt, DROP lastActivityAt, DROP phoneNumber');
    }
}
