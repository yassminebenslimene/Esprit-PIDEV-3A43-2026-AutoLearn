<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225163923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revisions (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE challenge_audit (id INT NOT NULL, titre VARCHAR(20) DEFAULT NULL, description VARCHAR(50) DEFAULT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, niveau VARCHAR(15) DEFAULT NULL, created_by INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_db011909d4e010eb903590a75c987dc3_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE chapitre_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, ordre INT DEFAULT NULL, ressources VARCHAR(255) DEFAULT NULL, ressource_type VARCHAR(50) DEFAULT NULL, ressource_fichier VARCHAR(255) DEFAULT NULL, cours_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_a5ff8cd8e0ba453667e99ba986624a71_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commentaire_audit (id INT NOT NULL, contenu LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, post_id INT DEFAULT NULL, user_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_ccf89f44954efd101afc123a05481209_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_audit (id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, owner_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_8de2424906ebf6ecf7620d0c949f6db2_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE communaute_members_audit (communaute_id INT NOT NULL, user_id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_c5f1d6693fbff2a978fcf479efab9b8d_idx (rev), PRIMARY KEY(communaute_id, user_id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, matiere VARCHAR(255) DEFAULT NULL, niveau VARCHAR(50) DEFAULT NULL, duree INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, communaute_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_f0a9c376f672b1f03a90e8f084638555_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_audit (id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, evenement_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_c24675a29698eb6a007bafea904a789e_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_etudiant_audit (equipe_id INT NOT NULL, etudiant_id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_be9aa8a190497af8696978efc0fedb29_idx (rev), PRIMARY KEY(equipe_id, etudiant_id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenement_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, is_canceled TINYINT(1) DEFAULT NULL, nb_max INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_dbaa30077996693853517c2ad5c54ac3_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercice_audit (id INT NOT NULL, question VARCHAR(30) DEFAULT NULL, reponse VARCHAR(30) DEFAULT NULL, points INT DEFAULT NULL, challenge_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_37949d4cec2827a14862b737d2807f1e_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_audit (id INT NOT NULL, contenu LONGTEXT DEFAULT NULL, image_file VARCHAR(255) DEFAULT NULL, video_file VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, communaute_id INT DEFAULT NULL, user_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_7d2ab6760afca296cbe1bbe3d5f25777_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, etat VARCHAR(50) DEFAULT NULL, duree_max_minutes INT DEFAULT NULL, seuil_reussite INT DEFAULT NULL, max_tentatives INT DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, chapitre_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_9a1045a30df1369ec088d1792a5fd9fa_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource_audit (id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, lien VARCHAR(500) DEFAULT NULL, fichier VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, chapitre_id INT DEFAULT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, INDEX rev_108d6b4f56ab02df8f988c1ca098eb26_idx (rev), PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE challenge_audit ADD CONSTRAINT rev_db011909d4e010eb903590a75c987dc3_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE chapitre_audit ADD CONSTRAINT rev_a5ff8cd8e0ba453667e99ba986624a71_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE commentaire_audit ADD CONSTRAINT rev_ccf89f44954efd101afc123a05481209_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE communaute_audit ADD CONSTRAINT rev_8de2424906ebf6ecf7620d0c949f6db2_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE cours_audit ADD CONSTRAINT rev_f0a9c376f672b1f03a90e8f084638555_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE equipe_audit ADD CONSTRAINT rev_c24675a29698eb6a007bafea904a789e_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE evenement_audit ADD CONSTRAINT rev_dbaa30077996693853517c2ad5c54ac3_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE exercice_audit ADD CONSTRAINT rev_37949d4cec2827a14862b737d2807f1e_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE post_audit ADD CONSTRAINT rev_7d2ab6760afca296cbe1bbe3d5f25777_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE quiz_audit ADD CONSTRAINT rev_9a1045a30df1369ec088d1792a5fd9fa_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE ressource_audit ADD CONSTRAINT rev_108d6b4f56ab02df8f988c1ca098eb26_fk FOREIGN KEY (rev) REFERENCES revisions (id)');
        $this->addSql('ALTER TABLE post DROP image_url, DROP video_url');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge_audit DROP FOREIGN KEY rev_db011909d4e010eb903590a75c987dc3_fk');
        $this->addSql('ALTER TABLE chapitre_audit DROP FOREIGN KEY rev_a5ff8cd8e0ba453667e99ba986624a71_fk');
        $this->addSql('ALTER TABLE commentaire_audit DROP FOREIGN KEY rev_ccf89f44954efd101afc123a05481209_fk');
        $this->addSql('ALTER TABLE communaute_audit DROP FOREIGN KEY rev_8de2424906ebf6ecf7620d0c949f6db2_fk');
        $this->addSql('ALTER TABLE cours_audit DROP FOREIGN KEY rev_f0a9c376f672b1f03a90e8f084638555_fk');
        $this->addSql('ALTER TABLE equipe_audit DROP FOREIGN KEY rev_c24675a29698eb6a007bafea904a789e_fk');
        $this->addSql('ALTER TABLE evenement_audit DROP FOREIGN KEY rev_dbaa30077996693853517c2ad5c54ac3_fk');
        $this->addSql('ALTER TABLE exercice_audit DROP FOREIGN KEY rev_37949d4cec2827a14862b737d2807f1e_fk');
        $this->addSql('ALTER TABLE post_audit DROP FOREIGN KEY rev_7d2ab6760afca296cbe1bbe3d5f25777_fk');
        $this->addSql('ALTER TABLE quiz_audit DROP FOREIGN KEY rev_9a1045a30df1369ec088d1792a5fd9fa_fk');
        $this->addSql('ALTER TABLE ressource_audit DROP FOREIGN KEY rev_108d6b4f56ab02df8f988c1ca098eb26_fk');
        $this->addSql('DROP TABLE revisions');
        $this->addSql('DROP TABLE challenge_audit');
        $this->addSql('DROP TABLE chapitre_audit');
        $this->addSql('DROP TABLE commentaire_audit');
        $this->addSql('DROP TABLE communaute_audit');
        $this->addSql('DROP TABLE communaute_members_audit');
        $this->addSql('DROP TABLE cours_audit');
        $this->addSql('DROP TABLE equipe_audit');
        $this->addSql('DROP TABLE equipe_etudiant_audit');
        $this->addSql('DROP TABLE evenement_audit');
        $this->addSql('DROP TABLE exercice_audit');
        $this->addSql('DROP TABLE post_audit');
        $this->addSql('DROP TABLE quiz_audit');
        $this->addSql('DROP TABLE ressource_audit');
        $this->addSql('ALTER TABLE post ADD image_url VARCHAR(255) DEFAULT NULL, ADD video_url VARCHAR(255) DEFAULT NULL');
    }
}
