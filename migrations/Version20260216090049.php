<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216090049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE cours');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951B03A8386');
        $this->addSql('DROP INDEX IDX_D7098951B03A8386 ON challenge');
        $this->addSql('ALTER TABLE challenge CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951DE12AB56 FOREIGN KEY (created_by) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_D7098951DE12AB56 ON challenge (created_by)');
        $this->addSql('ALTER TABLE exercice CHANGE challenge_id challenge_id INT NOT NULL');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA921FBEEF7B');
        $this->addSql('DROP INDEX IDX_A412FA921FBEEF7B ON quiz');
        $this->addSql('ALTER TABLE quiz DROP chapitre_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, contenu LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ordre INT NOT NULL, ressources VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, cours_id INT NOT NULL, INDEX IDX_8C62B0257ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, matiere VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, niveau VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, duree INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951DE12AB56');
        $this->addSql('DROP INDEX IDX_D7098951DE12AB56 ON challenge');
        $this->addSql('ALTER TABLE challenge CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_D7098951B03A8386 ON challenge (created_by_id)');
        $this->addSql('ALTER TABLE exercice CHANGE challenge_id challenge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD chapitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('CREATE INDEX IDX_A412FA921FBEEF7B ON quiz (chapitre_id)');
    }
}
