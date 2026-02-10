<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210125456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, lieu VARCHAR(100) NOT NULL, capacite_max INT NOT NULL, statut VARCHAR(255) NOT NULL, is_canceled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, nom VARCHAR(150) NOT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2449BA15FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_etudiant (equipe_id INT NOT NULL, etudiant_id INT NOT NULL, INDEX IDX_B371DD586D861B89 (equipe_id), INDEX IDX_B371DD58DDEAB1A3 (etudiant_id), PRIMARY KEY(equipe_id, etudiant_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, equipe_id INT NOT NULL, date_inscription DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', commentaire_admin LONGTEXT DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_AB55E24FFD02F13 (evenement_id), INDEX IDX_AB55E24F6D861B89 (equipe_id), UNIQUE INDEX unique_participation (evenement_id, equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD586D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD58DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15FD02F13');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD586D861B89');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD58DDEAB1A3');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6D861B89');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE equipe_etudiant');
        $this->addSql('DROP TABLE participation');
    }
}
