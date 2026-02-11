<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211205430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipe_etudiant (equipe_id INT NOT NULL, etudiant_id INT NOT NULL, INDEX IDX_B371DD586D861B89 (equipe_id), INDEX IDX_B371DD58DDEAB1A3 (etudiant_id), PRIMARY KEY (equipe_id, etudiant_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD586D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD58DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY `FK_D7098951B03A8386`');
        $this->addSql('DROP INDEX IDX_D7098951B03A8386 ON challenge');
        $this->addSql('ALTER TABLE challenge CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D7098951B03A8386 ON challenge (created_by_id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE user CHANGE niveau niveau VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD586D861B89');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD58DDEAB1A3');
        $this->addSql('DROP TABLE equipe_etudiant');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951B03A8386');
        $this->addSql('DROP INDEX IDX_D7098951B03A8386 ON challenge');
        $this->addSql('ALTER TABLE challenge CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT `FK_D7098951B03A8386` FOREIGN KEY (created_by) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_D7098951B03A8386 ON challenge (created_by)');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6D861B89');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('ALTER TABLE user CHANGE niveau niveau VARCHAR(50) DEFAULT NULL');
    }
}
