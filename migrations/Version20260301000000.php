<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user_challenge table for tracking user progress in challenges';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_challenge (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            challenge_id INT NOT NULL, 
            current_index INT DEFAULT NULL, 
            answers JSON DEFAULT NULL, 
            completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            score INT DEFAULT NULL, 
            total_points INT DEFAULT NULL, 
            started_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_user_challenge_user (user_id), 
            INDEX IDX_user_challenge_challenge (challenge_id), 
            UNIQUE INDEX UNIQ_user_challenge_user_challenge (user_id, challenge_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_user_challenge_user FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_user_challenge_challenge FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_user_challenge_user');
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_user_challenge_challenge');
        $this->addSql('DROP TABLE user_challenge');
    }
}
