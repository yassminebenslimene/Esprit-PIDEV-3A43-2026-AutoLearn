<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301093706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951DE12AB56');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951DE12AB56 FOREIGN KEY (created_by) REFERENCES user (userId) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE challenge_audit ADD CONSTRAINT rev_db011909d4e010eb903590a75c987dc3_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0257ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE chapitre_audit ADD CONSTRAINT rev_a5ff8cd8e0ba453667e99ba986624a71_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE chapitre_traduction ADD CONSTRAINT FK_A3FB62CB1FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F43A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE chapter_progress ADD CONSTRAINT FK_C4189F431FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE commentaire ADD sentiment VARCHAR(20) DEFAULT NULL, ADD sentiment_score DOUBLE PRECISION DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_67F068BCA76ED395 ON commentaire (user_id)');
        $this->addSql('ALTER TABLE commentaire_audit ADD CONSTRAINT rev_ccf89f44954efd101afc123a05481209_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE communaute ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE communaute ADD CONSTRAINT FK_21C947997E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_21C947997E3C61F9 ON communaute (owner_id)');
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
        $this->addSql('ALTER TABLE post ADD titre VARCHAR(255) DEFAULT NULL, ADD ai_reaction VARCHAR(50) DEFAULT NULL, ADD ai_reaction_data JSON DEFAULT NULL, ADD summary LONGTEXT DEFAULT NULL, ADD user_id INT DEFAULT NULL, DROP image_url, DROP video_url');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('ALTER TABLE post_audit ADD CONSTRAINT rev_7d2ab6760afca296cbe1bbe3d5f25777_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE post_reaction ADD CONSTRAINT FK_1B3A8E56A76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE post_reaction ADD CONSTRAINT FK_1B3A8E564B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD audio_name VARCHAR(255) DEFAULT NULL, ADD audio_size INT DEFAULT NULL, ADD video_name VARCHAR(255) DEFAULT NULL, ADD video_size INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD duree_max_minutes INT DEFAULT NULL, ADD seuil_reussite INT DEFAULT NULL, ADD max_tentatives INT DEFAULT NULL, ADD chapitre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA921FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id)');
        $this->addSql('CREATE INDEX IDX_A412FA921FBEEF7B ON quiz (chapitre_id)');
        $this->addSql('ALTER TABLE quiz_audit ADD CONSTRAINT rev_9a1045a30df1369ec088d1792a5fd9fa_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45441FBEEF7B FOREIGN KEY (chapitre_id) REFERENCES chapitre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ressource_audit ADD CONSTRAINT rev_108d6b4f56ab02df8f988c1ca098eb26_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE user ADD isSuspended TINYINT(1) DEFAULT 0 NOT NULL, ADD suspendedAt DATETIME DEFAULT NULL, ADD suspensionReason VARCHAR(500) DEFAULT NULL, ADD suspendedBy INT DEFAULT NULL, ADD lastLoginAt DATETIME DEFAULT NULL, ADD lastActivityAt DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5AA76ED395 FOREIGN KEY (user_id) REFERENCES user (userId)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D7098951DE12AB56');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D7098951DE12AB56 FOREIGN KEY (created_by) REFERENCES user (userId)');
        $this->addSql('ALTER TABLE challenge_audit DROP FOREIGN KEY rev_db011909d4e010eb903590a75c987dc3_fk');
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0257ECF78B0');
        $this->addSql('ALTER TABLE chapitre_audit DROP FOREIGN KEY rev_a5ff8cd8e0ba453667e99ba986624a71_fk');
        $this->addSql('ALTER TABLE chapitre_traduction DROP FOREIGN KEY FK_A3FB62CB1FBEEF7B');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F43A76ED395');
        $this->addSql('ALTER TABLE chapter_progress DROP FOREIGN KEY FK_C4189F431FBEEF7B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('DROP INDEX IDX_67F068BCA76ED395 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP sentiment, DROP sentiment_score, DROP user_id');
        $this->addSql('ALTER TABLE commentaire_audit DROP FOREIGN KEY rev_ccf89f44954efd101afc123a05481209_fk');
        $this->addSql('ALTER TABLE communaute DROP FOREIGN KEY FK_21C947997E3C61F9');
        $this->addSql('DROP INDEX IDX_21C947997E3C61F9 ON communaute');
        $this->addSql('ALTER TABLE communaute DROP owner_id');
        $this->addSql('ALTER TABLE communaute_audit DROP FOREIGN KEY rev_8de2424906ebf6ecf7620d0c949f6db2_fk');
        $this->addSql('ALTER TABLE communaute_members DROP FOREIGN KEY FK_4ACD9E67C903E5B8');
        $this->addSql('ALTER TABLE communaute_members DROP FOREIGN KEY FK_4ACD9E67A76ED395');
        $this->addSql('ALTER TABLE communaute_pending_members DROP FOREIGN KEY FK_C73B1E72C903E5B8');
        $this->addSql('ALTER TABLE communaute_pending_members DROP FOREIGN KEY FK_C73B1E72A76ED395');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CC903E5B8');
        $this->addSql('ALTER TABLE cours_audit DROP FOREIGN KEY rev_f0a9c376f672b1f03a90e8f084638555_fk');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15FD02F13');
        $this->addSql('ALTER TABLE equipe_audit DROP FOREIGN KEY rev_c24675a29698eb6a007bafea904a789e_fk');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD586D861B89');
        $this->addSql('ALTER TABLE equipe_etudiant DROP FOREIGN KEY FK_B371DD58DDEAB1A3');
        $this->addSql('ALTER TABLE evenement_audit DROP FOREIGN KEY rev_dbaa30077996693853517c2ad5c54ac3_fk');
        $this->addSql('ALTER TABLE exercice_audit DROP FOREIGN KEY rev_37949d4cec2827a14862b737d2807f1e_fk');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F6D861B89');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        $this->addSql('ALTER TABLE post ADD video_url VARCHAR(255) DEFAULT NULL, DROP ai_reaction, DROP ai_reaction_data, DROP summary, DROP user_id, CHANGE titre image_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post_audit DROP FOREIGN KEY rev_7d2ab6760afca296cbe1bbe3d5f25777_fk');
        $this->addSql('ALTER TABLE post_reaction DROP FOREIGN KEY FK_1B3A8E56A76ED395');
        $this->addSql('ALTER TABLE post_reaction DROP FOREIGN KEY FK_1B3A8E564B89032C');
        $this->addSql('ALTER TABLE question DROP image_name, DROP image_size, DROP audio_name, DROP audio_size, DROP video_name, DROP video_size, DROP updated_at');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA921FBEEF7B');
        $this->addSql('DROP INDEX IDX_A412FA921FBEEF7B ON quiz');
        $this->addSql('ALTER TABLE quiz DROP duree_max_minutes, DROP seuil_reussite, DROP max_tentatives, DROP chapitre_id');
        $this->addSql('ALTER TABLE quiz_audit DROP FOREIGN KEY rev_9a1045a30df1369ec088d1792a5fd9fa_fk');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45441FBEEF7B');
        $this->addSql('ALTER TABLE ressource_audit DROP FOREIGN KEY rev_108d6b4f56ab02df8f988c1ca098eb26_fk');
        $this->addSql('ALTER TABLE user DROP isSuspended, DROP suspendedAt, DROP suspensionReason, DROP suspendedBy, DROP lastLoginAt, DROP lastActivityAt');
        $this->addSql('ALTER TABLE user_activity DROP FOREIGN KEY FK_4CF9ED5AA76ED395');
    }
}
