<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210201533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15FD02F13');
        $this->addSql('ALTER TABLE equipe CHANGE date_creation date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD586D861B89');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD58DDEAB1A3');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD586D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD58DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6D861B89');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('DROP INDEX unique_participation ON participation');
        $this->addSql('ALTER TABLE participation CHANGE date_inscription date_inscription DATETIME NOT NULL');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE user CHANGE niveau niveau VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15FD02F13');
        $this->addSql('ALTER TABLE equipe CHANGE date_creation date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD586D861B89');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD58DDEAB1A3');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD586D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD58DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6D861B89');
        $this->addSql('ALTER TABLE participation CHANGE date_inscription date_inscription DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX unique_participation ON participation (evenement_id, equipe_id)');
        $this->addSql('ALTER TABLE user CHANGE niveau niveau VARCHAR(50) DEFAULT NULL');
    }
}
