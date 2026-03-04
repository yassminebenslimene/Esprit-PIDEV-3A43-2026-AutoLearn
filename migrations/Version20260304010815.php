<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304010815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Skip creating audit tables as they already exist
        // $this->addSql('CREATE TABLE communaute_members_audit ...');
        // $this->addSql('CREATE TABLE communaute_pending_members_audit ...');
        // $this->addSql('CREATE TABLE equipe_etudiant_audit ...');
        
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951DE12AB56');
        $this->addSql('DROP INDEX IDX_D7098951DE12AB56 ON challenge');
        $this->addSql('ALTER TABLE challenge CHANGE created_by created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D7098951B03A8386 ON challenge (created_by_id)');
        
        // Update audit table column name
        $this->addSql('ALTER TABLE challenge_audit CHANGE created_by created_by_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951B03A8386');
        $this->addSql('DROP INDEX IDX_D7098951B03A8386 ON challenge');
        $this->addSql('ALTER TABLE challenge CHANGE created_by_id created_by INT NOT NULL');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951DE12AB56 FOREIGN KEY (created_by) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D7098951DE12AB56 ON challenge (created_by)');
        
        // Revert audit table column name
        $this->addSql('ALTER TABLE challenge_audit CHANGE created_by_id created_by INT DEFAULT NULL');
    }
}
