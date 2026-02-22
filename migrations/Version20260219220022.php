<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260219220022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapitre_traduction (id INT AUTO_INCREMENT NOT NULL, langue VARCHAR(5) NOT NULL, titre_traduit VARCHAR(500) NOT NULL, contenu_traduit LONGTEXT NOT NULL, created_at DATETIME NOT NULL, chapitre_id INT NOT NULL, INDEX IDX_A3FB62CB1FBEEF7B (chapitre_id), INDEX idx_chapitre_langue (chapitre_id, langue), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, lien VARCHAR(500) DEFAULT NULL, fichier VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, chapitre_id INT NOT NULL, INDEX IDX_939F45441FBEEF7B (chapitre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chapitre_traduction ADD CONSTRAINT FK_A3FB62CB1FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45441FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communaute CHANGE owner_id owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD communaute_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CC903E5B8 FOREIGN KEY (communaute_id) REFERENCES communaute (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FDCA8C9CC903E5B8 ON cours (communaute_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre_traduction DROP FOREIGN KEY FK_A3FB62CB1FBEEF7B');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45441FBEEF7B');
        $this->addSql('DROP TABLE chapitre_traduction');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('ALTER TABLE communaute CHANGE owner_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CC903E5B8');
        $this->addSql('DROP INDEX UNIQ_FDCA8C9CC903E5B8 ON cours');
        $this->addSql('ALTER TABLE cours DROP communaute_id');
    }
}
