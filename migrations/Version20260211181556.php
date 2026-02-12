<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211181556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Créer d'abord les tables sans dépendances
        $this->addSql('CREATE TABLE communaute (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, matiere VARCHAR(255) NOT NULL, niveau VARCHAR(50) NOT NULL, duree INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, etat VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Créer les tables avec dépendances simples
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, ordre INT NOT NULL, ressources VARCHAR(255) DEFAULT NULL, cours_id INT NOT NULL, INDEX IDX_8C62B0257ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, image_url VARCHAR(255) DEFAULT NULL, video_url VARCHAR(255) DEFAULT NULL, image_file VARCHAR(255) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, communaute_id INT NOT NULL, INDEX IDX_5A8A6C8DC903E5B8 (communaute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, texte_question LONGTEXT NOT NULL, point INT NOT NULL, quiz_id INT NOT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Créer les tables avec dépendances multiples
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, created_at DATETIME NOT NULL, post_id INT NOT NULL, INDEX IDX_67F068BC4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, texte_option VARCHAR(255) NOT NULL, est_correcte TINYINT(1) NOT NULL, question_id INT NOT NULL, INDEX IDX_5A8600B01E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Créer challenge et exercice en dernier (dépendent de user qui existe déjà)
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(20) NOT NULL, description VARCHAR(50) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, niveau VARCHAR(15) NOT NULL, created_by_id INT DEFAULT NULL, INDEX IDX_D7098951B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercice (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(30) NOT NULL, reponse VARCHAR(30) NOT NULL, points INT NOT NULL, challenge_id INT NOT NULL, INDEX IDX_E418C74D98A21AC6 (challenge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Ajouter les contraintes de clés étrangères
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0257ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE `option` ADD CONSTRAINT FK_5A8600B01E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC903E5B8 FOREIGN KEY (communaute_id) REFERENCES communaute (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_E418C74D98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        
        // Modifier la table user si nécessaire
        $this->addSql('ALTER TABLE user CHANGE niveau niveau VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951B03A8386');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0257ECF78B0');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC4B89032C');
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_E418C74D98A21AC6');
        $this->addSql('ALTER TABLE `option` DROP FOREIGN KEY FK_5A8600B01E27F6BF');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC903E5B8');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE communaute');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE exercice');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('ALTER TABLE user CHANGE niveau niveau VARCHAR(50) DEFAULT NULL');
    }
}
