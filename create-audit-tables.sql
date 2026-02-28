-- Tables d'audit pour le système AutoLearn
-- Bundle: SimpleThingsEntityAuditBundle

-- Table principale des révisions (déjà créée normalement)
CREATE TABLE IF NOT EXISTS revisions (
    id INT AUTO_INCREMENT NOT NULL,
    timestamp DATETIME NOT NULL,
    username VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4;

-- User audit
CREATE TABLE IF NOT EXISTS user_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    username VARCHAR(180) DEFAULT NULL,
    roles JSON DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    nom VARCHAR(255) DEFAULT NULL,
    prenom VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Etudiant audit
CREATE TABLE IF NOT EXISTS etudiant_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    niveau VARCHAR(255) DEFAULT NULL,
    specialite VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Admin audit
CREATE TABLE IF NOT EXISTS admin_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    role_admin VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Cours audit
CREATE TABLE IF NOT EXISTS cours_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    niveau VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Chapitre audit
CREATE TABLE IF NOT EXISTS chapitre_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    contenu LONGTEXT DEFAULT NULL,
    ordre INT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Ressource audit
CREATE TABLE IF NOT EXISTS ressource_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    type VARCHAR(50) DEFAULT NULL,
    url VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Quiz audit
CREATE TABLE IF NOT EXISTS quiz_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Exercice audit
CREATE TABLE IF NOT EXISTS exercice_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Challenge audit
CREATE TABLE IF NOT EXISTS challenge_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Evenement audit
CREATE TABLE IF NOT EXISTS evenement_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    date_debut DATETIME DEFAULT NULL,
    date_fin DATETIME DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Communaute audit
CREATE TABLE IF NOT EXISTS communaute_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    nom VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Post audit
CREATE TABLE IF NOT EXISTS post_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    contenu LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Commentaire audit
CREATE TABLE IF NOT EXISTS commentaire_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    contenu LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;

-- Equipe audit
CREATE TABLE IF NOT EXISTS equipe_audit (
    id INT NOT NULL,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    nom VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id, rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) DEFAULT CHARACTER SET utf8mb4;
