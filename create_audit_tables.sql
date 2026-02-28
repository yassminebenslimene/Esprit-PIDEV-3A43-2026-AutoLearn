-- Create audit tables for all entities
-- Note: Using SINGLE_TABLE inheritance, so etudiant_audit and admin_audit share same structure

CREATE TABLE IF NOT EXISTS etudiant_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    userId INT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(180),
    password VARCHAR(255),
    role VARCHAR(20),
    createdAt DATETIME,
    isSuspended TINYINT(1),
    suspendedAt DATETIME,
    suspensionReason VARCHAR(500),
    suspendedBy INT,
    lastLoginAt DATETIME,
    niveau VARCHAR(20),
    discr VARCHAR(255),
    INDEX rev_idx (rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rev INT NOT NULL,
    revtype VARCHAR(4) NOT NULL,
    userId INT,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(180),
    password VARCHAR(255),
    role VARCHAR(20),
    createdAt DATETIME,
    isSuspended TINYINT(1),
    suspendedAt DATETIME,
    suspensionReason VARCHAR(500),
    suspendedBy INT,
    lastLoginAt DATETIME,
    discr VARCHAR(255),
    INDEX rev_idx (rev),
    FOREIGN KEY (rev) REFERENCES revisions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
