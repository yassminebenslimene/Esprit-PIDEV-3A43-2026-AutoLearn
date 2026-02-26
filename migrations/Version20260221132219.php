<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260221132219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_USER_CHALLENGE_USER');
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_USER_CHALLENGE_CHALLENGE');
        $this->addSql('ALTER TABLE user_challenge CHANGE completed_at completed_at DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX idx_user_id ON user_challenge');
        $this->addSql('CREATE INDEX IDX_D7E904B5A76ED395 ON user_challenge (user_id)');
        $this->addSql('DROP INDEX idx_challenge_id ON user_challenge');
        $this->addSql('CREATE INDEX IDX_D7E904B598A21AC6 ON user_challenge (challenge_id)');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_USER_CHALLENGE_USER FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_USER_CHALLENGE_CHALLENGE FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_D7E904B5A76ED395');
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_D7E904B598A21AC6');
        $this->addSql('ALTER TABLE user_challenge CHANGE completed_at completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX idx_d7e904b5a76ed395 ON user_challenge');
        $this->addSql('CREATE INDEX IDX_USER_ID ON user_challenge (user_id)');
        $this->addSql('DROP INDEX idx_d7e904b598a21ac6 ON user_challenge');
        $this->addSql('CREATE INDEX IDX_CHALLENGE_ID ON user_challenge (challenge_id)');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_D7E904B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_D7E904B598A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
    }
}
