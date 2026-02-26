<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20260219233502.php
final class Version20260219233502 extends AbstractMigration
========
final class Version20260216095701 extends AbstractMigration
>>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f:migrations/Version20260216095701.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20260219233502.php
        $this->addSql('ALTER TABLE user ADD is_suspended TINYINT(1) DEFAULT 0 NOT NULL, ADD suspended_at DATETIME DEFAULT NULL, ADD suspension_reason VARCHAR(500) DEFAULT NULL, ADD suspended_by INT DEFAULT NULL');
========
        $this->addSql('ALTER TABLE quiz ADD challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9298A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('CREATE INDEX IDX_A412FA9298A21AC6 ON quiz (challenge_id)');
>>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f:migrations/Version20260216095701.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20260219233502.php
        $this->addSql('ALTER TABLE user DROP is_suspended, DROP suspended_at, DROP suspension_reason, DROP suspended_by');
========
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9298A21AC6');
        $this->addSql('DROP INDEX IDX_A412FA9298A21AC6 ON quiz');
        $this->addSql('ALTER TABLE quiz DROP challenge_id');
>>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f:migrations/Version20260216095701.php
    }
}
