@echo off
cd /d "%~dp0"

echo Creating audit tables...
echo.

echo 1. Creating etudiant_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS etudiant_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, userId INT, nom VARCHAR(50), prenom VARCHAR(50), email VARCHAR(180), password VARCHAR(255), role VARCHAR(20), createdAt DATETIME, isSuspended TINYINT(1), suspendedAt DATETIME, suspensionReason VARCHAR(500), suspendedBy INT, lastLoginAt DATETIME, niveau VARCHAR(20), discr VARCHAR(255), INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo 2. Creating admin_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS admin_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, userId INT, nom VARCHAR(50), prenom VARCHAR(50), email VARCHAR(180), password VARCHAR(255), role VARCHAR(20), createdAt DATETIME, isSuspended TINYINT(1), suspendedAt DATETIME, suspensionReason VARCHAR(500), suspendedBy INT, lastLoginAt DATETIME, discr VARCHAR(255), INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo 3. Creating cours_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS cours_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255), description TEXT, niveau VARCHAR(50), INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo 4. Creating chapitre_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS chapitre_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255), contenu TEXT, INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo 5. Creating challenge_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS challenge_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255), description TEXT, difficulte VARCHAR(50), INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo 6. Creating evenement_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS evenement_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255), description TEXT, dateDebut DATETIME, dateFin DATETIME, INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo 7. Creating communaute_audit table...
php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS communaute_audit (id INT AUTO_INCREMENT PRIMARY KEY, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, nom VARCHAR(255), description TEXT, INDEX rev_idx (rev), FOREIGN KEY (rev) REFERENCES revisions(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

echo.
echo All audit tables created successfully!
echo.
echo Now testing: Modify a student in backoffice to see if audit tracking works.
echo.
pause
