# 📚 Restore Deleted Courses

## Problem
All courses (cours) have been deleted from the database in the web branch.

## Solution

I've created a fixtures file with 10 sample courses to restore your database.

---

## Quick Fix (Restore Courses)

### Option 1: Using Doctrine Fixtures (Recommended)

#### Step 1: Install Doctrine Fixtures Bundle (if not installed)
```bash
cd autolearn
composer require --dev doctrine/doctrine-fixtures-bundle
```

#### Step 2: Load the Fixtures
```bash
php bin/console doctrine:fixtures:load
```

**Warning**: This will DELETE all existing data and reload fixtures!

If you want to APPEND without deleting:
```bash
php bin/console doctrine:fixtures:load --append
```

### Option 2: Manual SQL Insert

If you prefer to manually insert courses, run this SQL:

```sql
INSERT INTO cours (titre, description, matiere, niveau, duree, created_at) VALUES
('Python pour Débutants', 'Apprenez les bases de la programmation Python. Ce cours couvre les variables, les boucles, les fonctions et les structures de données fondamentales.', 'Programmation', 'Débutant', 40, NOW()),
('JavaScript Moderne (ES6+)', 'Maîtrisez JavaScript moderne avec ES6+. Découvrez les arrow functions, les promesses, async/await, les modules et bien plus.', 'Développement Web', 'Intermédiaire', 50, NOW()),
('Développement Web avec React', 'Créez des applications web modernes avec React. Apprenez les composants, les hooks, le state management.', 'Framework Frontend', 'Intermédiaire', 60, NOW()),
('Bases de Données SQL', 'Apprenez à concevoir et gérer des bases de données relationnelles. Maîtrisez SQL, les requêtes complexes.', 'Base de données', 'Débutant', 35, NOW()),
('PHP et Symfony Framework', 'Développez des applications web robustes avec PHP et Symfony. Découvrez l\'architecture MVC, Doctrine ORM.', 'Backend Development', 'Intermédiaire', 70, NOW()),
('Git et GitHub pour Développeurs', 'Maîtrisez le contrôle de version avec Git et GitHub. Apprenez les branches, les merges, les pull requests.', 'Outils de développement', 'Débutant', 25, NOW()),
('Java et Programmation Orientée Objet', 'Apprenez Java et les concepts de la POO. Classes, héritage, polymorphisme, interfaces.', 'Programmation', 'Intermédiaire', 55, NOW()),
('HTML5 et CSS3 Fondamentaux', 'Créez des pages web modernes avec HTML5 et CSS3. Apprenez Flexbox, Grid, et les animations.', 'Développement Web', 'Débutant', 30, NOW()),
('Node.js et Express Backend', 'Développez des APIs REST avec Node.js et Express. Créez des serveurs et gérez les routes.', 'Backend Development', 'Intermédiaire', 45, NOW()),
('Introduction à l\'Intelligence Artificielle', 'Découvrez les fondamentaux de l\'IA et du Machine Learning avec Python.', 'Intelligence Artificielle', 'Avancé', 80, NOW());
```

### Option 3: Using phpMyAdmin

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select database: `autolearn_db`
3. Click on `cours` table
4. Click "Insert" tab
5. Fill in the form for each course
6. Click "Go" to insert

---

## Courses Created

The fixtures file creates 10 courses:

1. **Python pour Débutants** (40h) - Débutant
2. **JavaScript Moderne (ES6+)** (50h) - Intermédiaire
3. **Développement Web avec React** (60h) - Intermédiaire
4. **Bases de Données SQL** (35h) - Débutant
5. **PHP et Symfony Framework** (70h) - Intermédiaire
6. **Git et GitHub pour Développeurs** (25h) - Débutant
7. **Java et Programmation Orientée Objet** (55h) - Intermédiaire
8. **HTML5 et CSS3 Fondamentaux** (30h) - Débutant
9. **Node.js et Express Backend** (45h) - Intermédiaire
10. **Introduction à l'Intelligence Artificielle** (80h) - Avancé

---

## Verify Courses Are Restored

### Check in Browser:
1. Go to: http://localhost:8000/
2. Scroll to "Cours" section
3. You should see the courses displayed

### Check in Database:
```bash
php bin/console doctrine:query:sql "SELECT * FROM cours"
```

Or in phpMyAdmin:
1. Open phpMyAdmin
2. Select `autolearn_db`
3. Click on `cours` table
4. Click "Browse" to see all courses

---

## Why Were Courses Deleted?

Possible reasons:
1. **Database reset**: Someone ran `doctrine:schema:drop` or `doctrine:fixtures:load` without `--append`
2. **Migration issue**: A migration might have dropped the table
3. **Manual deletion**: Courses were deleted through the admin panel or phpMyAdmin
4. **Cascade delete**: If a related entity was deleted with cascade rules

---

## Prevent Future Data Loss

### 1. Backup Database Regularly
```bash
# Backup
mysqldump -u root autolearn_db > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root autolearn_db < backup_20240101.sql
```

### 2. Use Fixtures for Development
Keep the `CoursFixtures.php` file so you can always restore sample data.

### 3. Separate Development and Production
- Use different databases for dev and production
- Never run destructive commands on production

### 4. Version Control Database Schema
- Keep migrations in Git
- Document any manual database changes

---

## File Created

**autolearn/src/DataFixtures/CoursFixtures.php**
- Contains 10 sample courses
- Can be loaded with `doctrine:fixtures:load`
- Safe to run multiple times with `--append`

---

## Quick Commands Reference

```bash
# Install fixtures bundle
composer require --dev doctrine/doctrine-fixtures-bundle

# Load fixtures (WARNING: Deletes existing data!)
php bin/console doctrine:fixtures:load

# Load fixtures without deleting (SAFE)
php bin/console doctrine:fixtures:load --append

# Check if courses exist
php bin/console doctrine:query:sql "SELECT COUNT(*) FROM cours"

# View all courses
php bin/console doctrine:query:sql "SELECT id, titre, niveau FROM cours"

# Backup database
mysqldump -u root autolearn_db > backup.sql

# Restore database
mysql -u root autolearn_db < backup.sql
```

---

## Summary

1. I created `CoursFixtures.php` with 10 sample courses
2. Run `composer require --dev doctrine/doctrine-fixtures-bundle`
3. Run `php bin/console doctrine:fixtures:load --append`
4. Courses will be restored!
5. Check your homepage to see them

Your courses are back! 🎉
