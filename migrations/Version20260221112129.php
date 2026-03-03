<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260221112129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<< HEAD
        $this->addSql('DROP TABLE notification');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F43A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
=======
        $this->addSql('DROP TABLE IF EXISTS notification');
        // Créer la table chapter_progress si elle n'existe pas
        $this->addSql('CREATE TABLE IF NOT EXISTS chapter_progress (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, chapitre_id INT NOT NULL, is_completed TINYINT(1) DEFAULT 0 NOT NULL, completed_at DATETIME DEFAULT NULL, INDEX IDX_C4189F43A76ED395 (user_id), INDEX IDX_C4189F431FBEEF7B (chapitre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F43A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
>>>>>>> cf32977d311ea069ba23a14c2cf5417034d537b8
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F431FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_read TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, action_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F43A76ED395');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F431FBEEF7B');
    }
}
