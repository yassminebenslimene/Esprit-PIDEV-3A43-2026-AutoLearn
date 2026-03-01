<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301092641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge_audit (id INT NOT NULL, titre VARCHAR(20) DEFAULT NULL, description VARCHAR(50) DEFAULT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, niveau VARCHAR(15) DEFAULT NULL, created_by INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_db011909d4e010eb903590a75c987dc3_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, ordre INT NOT NULL, ressources VARCHAR(255) DEFAULT NULL, ressource_type VARCHAR(50) DEFAULT NULL, ressource_fichier VARCHAR(255) DEFAULT NULL, cours_id INT NOT NULL, INDEX IDX_8C62B0257ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE chapitre_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, ordre INT DEFAULT NULL, ressources VARCHAR(255) DEFAULT NULL, ressource_type VARCHAR(50) DEFAULT NULL, ressource_fichier VARCHAR(255) DEFAULT NULL, cours_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_a5ff8cd8e0ba453667e99ba986624a71_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE chapitre_traduction (id INT AUTO_INCREMENT NOT NULL, langue VARCHAR(5) NOT NULL, titre_traduit VARCHAR(500) NOT NULL, contenu_traduit LONGTEXT NOT NULL, created_at DATETIME NOT NULL, chapitre_id INT NOT NULL, INDEX IDX_A3FB62CB1FBEEF7B (chapitre_id), INDEX idx_chapitre_langue (chapitre_id, langue), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE chapter_progress (id INT AUTO_INCREMENT NOT NULL, completed_at DATETIME DEFAULT NULL, quiz_score INT DEFAULT NULL, user_id INT NOT NULL, chapitre_id INT NOT NULL, INDEX IDX_C4189F43A76ED395 (user_id), INDEX IDX_C4189F431FBEEF7B (chapitre_id), UNIQUE INDEX user_chapter_unique (user_id, chapitre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commentaire_audit (id INT NOT NULL, contenu LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, sentiment VARCHAR(20) DEFAULT NULL, sentiment_score DOUBLE PRECISION DEFAULT NULL, post_id INT DEFAULT NULL, user_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_ccf89f44954efd101afc123a05481209_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_members (communaute_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4ACD9E67C903E5B8 (communaute_id), INDEX IDX_4ACD9E67A76ED395 (user_id), PRIMARY KEY(communaute_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_pending_members (communaute_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C73B1E72C903E5B8 (communaute_id), INDEX IDX_C73B1E72A76ED395 (user_id), PRIMARY KEY(communaute_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_audit (id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, owner_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_8de2424906ebf6ecf7620d0c949f6db2_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_members_audit (communaute_id INT NOT NULL, user_id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_c5f1d6693fbff2a978fcf479efab9b8d_idx (rev), PRIMARY KEY(communaute_id, user_id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_pending_members_audit (communaute_id INT NOT NULL, user_id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_b01655a98268b4d71e6d83a9fcbc19c5_idx (rev), PRIMARY KEY(communaute_id, user_id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, matiere VARCHAR(255) NOT NULL, niveau VARCHAR(50) NOT NULL, duree INT NOT NULL, created_at DATETIME NOT NULL, communaute_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_FDCA8C9CC903E5B8 (communaute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, matiere VARCHAR(255) DEFAULT NULL, niveau VARCHAR(50) DEFAULT NULL, duree INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, communaute_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_f0a9c376f672b1f03a90e8f084638555_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, evenement_id INT NOT NULL, INDEX IDX_2449BA15FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_etudiant (equipe_id INT NOT NULL, etudiant_id INT NOT NULL, INDEX IDX_B371DD586D861B89 (equipe_id), INDEX IDX_B371DD58DDEAB1A3 (etudiant_id), PRIMARY KEY(equipe_id, etudiant_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_audit (id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, evenement_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_c24675a29698eb6a007bafea904a789e_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_etudiant_audit (equipe_id INT NOT NULL, etudiant_id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_be9aa8a190497af8696978efc0fedb29_idx (rev), PRIMARY KEY(equipe_id, etudiant_id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, status VARCHAR(255) NOT NULL, is_canceled TINYINT(1) NOT NULL, workflow_status VARCHAR(50) NOT NULL, nb_max INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenement_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, is_canceled TINYINT(1) DEFAULT NULL, workflow_status VARCHAR(50) DEFAULT NULL, nb_max INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_dbaa30077996693853517c2ad5c54ac3_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercice_audit (id INT NOT NULL, question VARCHAR(30) DEFAULT NULL, reponse VARCHAR(30) DEFAULT NULL, points INT DEFAULT NULL, challenge_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_37949d4cec2827a14862b737d2807f1e_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(50) NOT NULL, feedbacks JSON DEFAULT NULL, equipe_id INT NOT NULL, evenement_id INT NOT NULL, INDEX IDX_AB55E24F6D861B89 (equipe_id), INDEX IDX_AB55E24FFD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_audit (id INT NOT NULL, contenu LONGTEXT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, ai_reaction VARCHAR(50) DEFAULT NULL, ai_reaction_data JSON DEFAULT NULL, summary LONGTEXT DEFAULT NULL, image_file VARCHAR(255) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, communaute_id INT DEFAULT NULL, user_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_7d2ab6760afca296cbe1bbe3d5f25777_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_reaction (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_1B3A8E56A76ED395 (user_id), INDEX IDX_1B3A8E564B89032C (post_id), UNIQUE INDEX user_post_unique (user_id, post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, etat VARCHAR(50) DEFAULT NULL, duree_max_minutes INT DEFAULT NULL, seuil_reussite INT DEFAULT NULL, max_tentatives INT DEFAULT NULL, chapitre_id INT DEFAULT NULL, challenge_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_9a1045a30df1369ec088d1792a5fd9fa_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, lien VARCHAR(500) DEFAULT NULL, fichier VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, chapitre_id INT NOT NULL, INDEX IDX_939F45441FBEEF7B (chapitre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, lien VARCHAR(500) DEFAULT NULL, fichier VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, chapitre_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_108d6b4f56ab02df8f988c1ca098eb26_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_activity (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(50) NOT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, metadata JSON DEFAULT NULL, created_at DATETIME NOT NULL, location VARCHAR(100) DEFAULT NULL, success TINYINT(1) NOT NULL, error_message VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_4CF9ED5AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE challenge_audit ADD CONSTRAINT rev_db011909d4e010eb903590a75c987dc3_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0257ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE chapitre_audit ADD CONSTRAINT rev_a5ff8cd8e0ba453667e99ba986624a71_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE chapitre_traduction ADD CONSTRAINT FK_A3FB62CB1FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F43A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F431FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE commentaire_audit ADD CONSTRAINT rev_ccf89f44954efd101afc123a05481209_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE communaute_members ADD CONSTRAINT FK_4ACD9E67C903E5B8 FOREIGN KEY (communaute_id) REFERENCES communaute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communaute_members ADD CONSTRAINT FK_4ACD9E67A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communaute_pending_members ADD CONSTRAINT FK_C73B1E72C903E5B8 FOREIGN KEY (communaute_id) REFERENCES communaute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communaute_pending_members ADD CONSTRAINT FK_C73B1E72A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE communaute_audit ADD CONSTRAINT rev_8de2424906ebf6ecf7620d0c949f6db2_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CC903E5B8 FOREIGN KEY (communaute_id) REFERENCES communaute (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cours_audit ADD CONSTRAINT rev_f0a9c376f672b1f03a90e8f084638555_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD586D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE equipe_etudiant ADD CONSTRAINT FK_B371DD58DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE equipe_audit ADD CONSTRAINT rev_c24675a29698eb6a007bafea904a789e_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE evenement_audit ADD CONSTRAINT rev_dbaa30077996693853517c2ad5c54ac3_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE exercice_audit ADD CONSTRAINT rev_37949d4cec2827a14862b737d2807f1e_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE post_audit ADD CONSTRAINT rev_7d2ab6760afca296cbe1bbe3d5f25777_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE post_reaction ADD CONSTRAINT FK_1B3A8E56A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE post_reaction ADD CONSTRAINT FK_1B3A8E564B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_audit ADD CONSTRAINT rev_9a1045a30df1369ec088d1792a5fd9fa_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45441FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource_audit ADD CONSTRAINT rev_108d6b4f56ab02df8f988c1ca098eb26_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5AA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951DE12AB56');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951DE12AB56 FOREIGN KEY (created_by) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire ADD sentiment VARCHAR(20) DEFAULT NULL, ADD sentiment_score DOUBLE PRECISION DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)');
        $this->addSql('ALTER TABLE communaute ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE communaute ADD CONSTRAINT FK_21C947997E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_21C947997E3C61F9 ON communaute (owner_id)');
        $this->addSql('ALTER TABLE post ADD titre VARCHAR(255) DEFAULT NULL, ADD ai_reaction VARCHAR(50) DEFAULT NULL, ADD ai_reaction_data JSON DEFAULT NULL, ADD summary LONGTEXT DEFAULT NULL, ADD user_id INT DEFAULT NULL, DROP image_url, DROP video_url');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('ALTER TABLE question ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD audio_name VARCHAR(255) DEFAULT NULL, ADD audio_size INT DEFAULT NULL, ADD video_name VARCHAR(255) DEFAULT NULL, ADD video_size INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD duree_max_minutes INT DEFAULT NULL, ADD seuil_reussite INT DEFAULT NULL, ADD max_tentatives INT DEFAULT NULL, ADD chapitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('CREATE INDEX IDX_A412FA921FBEEF7B ON quiz (chapitre_id)');
        $this->addSql('ALTER TABLE user ADD isSuspended TINYINT(1) DEFAULT 0 NOT NULL, ADD suspendedAt DATETIME DEFAULT NULL, ADD suspensionReason VARCHAR(500) DEFAULT NULL, ADD suspendedBy INT DEFAULT NULL, ADD lastLoginAt DATETIME DEFAULT NULL, ADD lastActivityAt DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_audit DROP FOREIGN KEY rev_db011909d4e010eb903590a75c987dc3_fk');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0257ECF78B0');
        $this->addSql('ALTER TABLE chapitre_audit DROP FOREIGN KEY rev_a5ff8cd8e0ba453667e99ba986624a71_fk');
        $this->addSql('ALTER TABLE chapitre_traduction DROP FOREIGN KEY FK_A3FB62CB1FBEEF7B');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F43A76ED395');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F431FBEEF7B');
        $this->addSql('ALTER TABLE commentaire_audit DROP FOREIGN KEY rev_ccf89f44954efd101afc123a05481209_fk');
        $this->addSql('ALTER TABLE communaute_members DROP FOREIGN KEY FK_4ACD9E67C903E5B8');
        $this->addSql('ALTER TABLE communaute_members DROP FOREIGN KEY FK_4ACD9E67A76ED395');
        $this->addSql('ALTER TABLE communaute_pending_members DROP FOREIGN KEY FK_C73B1E72C903E5B8');
        $this->addSql('ALTER TABLE communaute_pending_members DROP FOREIGN KEY FK_C73B1E72A76ED395');
        $this->addSql('ALTER TABLE communaute_audit DROP FOREIGN KEY rev_8de2424906ebf6ecf7620d0c949f6db2_fk');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CC903E5B8');
        $this->addSql('ALTER TABLE cours_audit DROP FOREIGN KEY rev_f0a9c376f672b1f03a90e8f084638555_fk');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15FD02F13');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD586D861B89');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD58DDEAB1A3');
        $this->addSql('ALTER TABLE equipe_audit DROP FOREIGN KEY rev_c24675a29698eb6a007bafea904a789e_fk');
        $this->addSql('ALTER TABLE evenement_audit DROP FOREIGN KEY rev_dbaa30077996693853517c2ad5c54ac3_fk');
        $this->addSql('ALTER TABLE exercice_audit DROP FOREIGN KEY rev_37949d4cec2827a14862b737d2807f1e_fk');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6D861B89');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('ALTER TABLE post_audit DROP FOREIGN KEY rev_7d2ab6760afca296cbe1bbe3d5f25777_fk');
        $this->addSql('ALTER TABLE post_reaction DROP FOREIGN KEY FK_1B3A8E56A76ED395');
        $this->addSql('ALTER TABLE post_reaction DROP FOREIGN KEY FK_1B3A8E564B89032C');
        $this->addSql('ALTER TABLE quiz_audit DROP FOREIGN KEY rev_9a1045a30df1369ec088d1792a5fd9fa_fk');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45441FBEEF7B');
        $this->addSql('ALTER TABLE ressource_audit DROP FOREIGN KEY rev_108d6b4f56ab02df8f988c1ca098eb26_fk');
        $this->addSql('ALTER TABLE user_activity DROP FOREIGN KEY FK_4CF9ED5AA76ED395');
        $this->addSql('DROP TABLE challenge_audit');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE chapitre_audit');
        $this->addSql('DROP TABLE chapitre_traduction');
        $this->addSql('DROP TABLE chapter_progress');
        $this->addSql('DROP TABLE commentaire_audit');
        $this->addSql('DROP TABLE communaute_members');
        $this->addSql('DROP TABLE communaute_pending_members');
        $this->addSql('DROP TABLE communaute_audit');
        $this->addSql('DROP TABLE communaute_members_audit');
        $this->addSql('DROP TABLE communaute_pending_members_audit');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_audit');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE equipe_etudiant');
        $this->addSql('DROP TABLE equipe_audit');
        $this->addSql('DROP TABLE equipe_etudiant_audit');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_audit');
        $this->addSql('DROP TABLE exercice_audit');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE post_audit');
        $this->addSql('DROP TABLE post_reaction');
        $this->addSql('DROP TABLE quiz_audit');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE ressource_audit');
        $this->addSql('DROP TABLE user_activity');
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951DE12AB56');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951DE12AB56 FOREIGN KEY (created_by) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('DROP INDEX IDX_67F068BCA76ED395 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP sentiment, DROP sentiment_score, DROP user_id');
        $this->addSql('ALTER TABLE communaute DROP FOREIGN KEY FK_21C947997E3C61F9');
        $this->addSql('DROP INDEX IDX_21C947997E3C61F9 ON communaute');
        $this->addSql('ALTER TABLE communaute DROP owner_id');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE post ADD video_url VARCHAR(255) DEFAULT NULL, DROP ai_reaction, DROP ai_reaction_data, DROP summary, DROP user_id, CHANGE titre image_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE question DROP image_name, DROP image_size, DROP audio_name, DROP audio_size, DROP video_name, DROP video_size, DROP updated_at');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA921FBEEF7B');
        $this->addSql('DROP INDEX IDX_A412FA921FBEEF7B ON quiz');
        $this->addSql('ALTER TABLE quiz DROP duree_max_minutes, DROP seuil_reussite, DROP max_tentatives, DROP chapitre_id');
        $this->addSql('ALTER TABLE user DROP isSuspended, DROP suspendedAt, DROP suspensionReason, DROP suspendedBy, DROP lastLoginAt, DROP lastActivityAt');
    }
}
