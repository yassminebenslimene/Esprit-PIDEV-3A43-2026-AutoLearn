<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260218210953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, ordre INT NOT NULL, ressources VARCHAR(255) DEFAULT NULL, ressource_type VARCHAR(50) DEFAULT NULL, ressource_fichier VARCHAR(255) DEFAULT NULL, cours_id INT NOT NULL, INDEX IDX_8C62B0257ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, matiere VARCHAR(255) NOT NULL, niveau VARCHAR(50) NOT NULL, duree INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0257ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE communaute ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE communaute ADD CONSTRAINT FK_21C947997E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_21C947997E3C61F9 ON communaute (owner_id)');
        $this->addSql('ALTER TABLE exercice CHANGE challenge_id challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD chapitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('CREATE INDEX IDX_A412FA921FBEEF7B ON quiz (chapitre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0257ECF78B0');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE cours');
        $this->addSql('ALTER TABLE communaute DROP FOREIGN KEY FK_21C947997E3C61F9');
        $this->addSql('DROP INDEX IDX_21C947997E3C61F9 ON communaute');
        $this->addSql('ALTER TABLE communaute DROP owner_id');
        $this->addSql('ALTER TABLE exercice CHANGE challenge_id challenge_id INT NOT NULL');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA921FBEEF7B');
        $this->addSql('DROP INDEX IDX_A412FA921FBEEF7B ON quiz');
        $this->addSql('ALTER TABLE quiz DROP chapitre_id');
    }
}
