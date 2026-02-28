<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211234219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE communaute_members (communaute_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4ACD9E67C903E5B8 (communaute_id), INDEX IDX_4ACD9E67A76ED395 (user_id), PRIMARY KEY(communaute_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE communaute_members ADD CONSTRAINT FK_4ACD9E67C903E5B8 FOREIGN KEY (communaute_id) REFERENCES communaute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communaute_members ADD CONSTRAINT FK_4ACD9E67A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE communaute_members DROP FOREIGN KEY FK_4ACD9E67C903E5B8');
        $this->addSql('ALTER TABLE communaute_members DROP FOREIGN KEY FK_4ACD9E67A76ED395');
        $this->addSql('DROP TABLE communaute_members');
    }
}
