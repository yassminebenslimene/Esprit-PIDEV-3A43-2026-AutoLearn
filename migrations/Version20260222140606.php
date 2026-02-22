<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222140606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, valeur INT NOT NULL, createdvote_at DATETIME NOT NULL, user_id INT NOT NULL, challenge_id INT NOT NULL, INDEX IDX_5A108564A76ED395 (user_id), INDEX IDX_5A10856498A21AC6 (challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856498A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856498A21AC6');
        $this->addSql('DROP TABLE vote');
    }
}
