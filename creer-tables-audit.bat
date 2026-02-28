@echo off
echo ========================================
echo CREATION TABLES AUDIT - Branche Ilef
echo ========================================
echo.

echo Creation de toutes les tables d'audit...
echo.

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS revisions (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS user_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, username VARCHAR(180) DEFAULT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS etudiant_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, niveau VARCHAR(255) DEFAULT NULL, specialite VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS admin_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, role_admin VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS cours_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, niveau VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS chapitre_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, ordre INT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS ressource_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, url VARCHAR(500) DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS quiz_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS exercice_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS challenge_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS evenement_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, date_debut DATETIME DEFAULT NULL, date_fin DATETIME DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS communaute_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS post_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, titre VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS commentaire_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, contenu LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

php bin/console doctrine:query:sql "CREATE TABLE IF NOT EXISTS equipe_audit (id INT NOT NULL, rev INT NOT NULL, revtype VARCHAR(4) NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id, rev), FOREIGN KEY (rev) REFERENCES revisions(id)) DEFAULT CHARACTER SET utf8mb4"

echo.
echo ========================================
echo TERMINE !
echo ========================================
echo.

echo Verification des tables creees...
php bin/console doctrine:query:sql "SHOW TABLES LIKE '%audit%'"
echo.

echo Vous pouvez maintenant tester le backoffice:
echo symfony server:start
echo http://localhost:8000/backoffice
echo.

pause
