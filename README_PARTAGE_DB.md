# 🚀 Partage de Base de Données - Guide Rapide

## 🎯 Méthode Recommandée: Fixtures Doctrine

### Pour celui qui partage les données:

```bash
# 1. Éditer les fixtures
# Ouvrir: src/DataFixtures/AppFixtures.php
# Ajouter vos événements, équipes, participations, feedbacks

# 2. Tester les fixtures
php bin/console doctrine:fixtures:load

# 3. Pusher sur Git
git add src/DataFixtures/
git commit -m "Update test data fixtures"
git push origin Amira
```

### Pour les camarades qui récupèrent:

```bash
# 1. Pull le code
git pull origin Amira

# 2. Créer la base de données (si pas encore fait)
php bin/console doctrine:database:create

# 3. Exécuter les migrations
php bin/console doctrine:migrations:migrate

# 4. Charger les fixtures
php bin/console doctrine:fixtures:load
```

---

## 📦 Méthode Alternative: Export/Import SQL

### Export (Partager vos données):

**Option 1: Script automatique**
```bash
# Double-cliquer sur: export_database.bat
# Le fichier SQL sera créé dans: database_backups/
```

**Option 2: Ligne de commande**
```bash
mysqldump -u root autolearn_db > backup.sql
```

**Option 3: phpMyAdmin**
1. Ouvrir http://localhost/phpmyadmin
2. Sélectionner `autolearn_db`
3. Cliquer "Exporter" → "Exécuter"

### Import (Récupérer les données):

**Option 1: Script automatique**
```bash
# 1. Placer le fichier .sql dans: database_backups/
# 2. Double-cliquer sur: import_database.bat
# 3. Entrer le nom du fichier
```

**Option 2: Ligne de commande**
```bash
mysql -u root autolearn_db < backup.sql
```

**Option 3: phpMyAdmin**
1. Ouvrir http://localhost/phpmyadmin
2. Sélectionner `autolearn_db`
3. Cliquer "Importer" → Choisir le fichier → "Exécuter"

---

## 📤 Partager le fichier SQL

- **Google Drive**: Créer un dossier partagé
- **Dropbox**: Générer un lien de partage
- **WeTransfer**: https://wetransfer.com/
- **Discord**: Glisser-déposer le fichier (< 8MB)

---

## ⚠️ Important

**NE JAMAIS pusher sur Git:**
- ❌ Fichiers `.sql`
- ❌ Dossier `database_backups/`
- ❌ Fichiers `.env` ou `.env.local`

**Toujours pusher sur Git:**
- ✅ Migrations (`migrations/`)
- ✅ Fixtures (`src/DataFixtures/`)
- ✅ Code source

---

## 🔄 Workflow Quotidien

```bash
# Matin: Récupérer les mises à jour
git pull
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# Soir: Partager vos modifications
git add .
git commit -m "Update features"
git push
```

---

## 📚 Documentation Complète

Voir: `GUIDE_PARTAGE_BASE_DONNEES.md` pour plus de détails.
