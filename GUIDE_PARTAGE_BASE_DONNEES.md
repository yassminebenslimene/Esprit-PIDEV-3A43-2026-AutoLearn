# 🗄️ Guide de Partage de Base de Données entre Camarades

## ❌ Pourquoi NE PAS pusher la base de données sur Git?

- **Taille**: Les fichiers de base de données sont trop volumineux pour Git
- **Sécurité**: Risque d'exposer des données sensibles
- **Conflits**: Impossible de merger des bases de données
- **Performance**: Git n'est pas conçu pour les données binaires

---

## ✅ Solutions Recommandées

### **Option 1: Fixtures Doctrine (RECOMMANDÉ)**

Les fixtures permettent de créer des données de test reproductibles.

#### Installation
```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

#### Utilisation

**1. Créer vos fixtures:**
```bash
# Le fichier src/DataFixtures/AppFixtures.php est déjà créé
```

**2. Charger les fixtures:**
```bash
php bin/console doctrine:fixtures:load
```

**3. Partager via Git:**
- Les fixtures sont du code PHP → peuvent être pushées sur Git
- Chaque camarade exécute `doctrine:fixtures:load` pour avoir les mêmes données

#### Avantages
- ✅ Versionnable avec Git
- ✅ Reproductible
- ✅ Pas de données sensibles
- ✅ Facile à maintenir

---

### **Option 2: Export/Import SQL**

#### Export de votre base de données

**Méthode 1: Via phpMyAdmin**
1. Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
2. Sélectionner votre base de données
3. Cliquer sur "Exporter"
4. Choisir "Rapide" ou "Personnalisé"
5. Format: SQL
6. Télécharger le fichier `.sql`

**Méthode 2: Via ligne de commande**
```bash
# Export complet
mysqldump -u root -p autolearn_db > backup_autolearn.sql

# Export de tables spécifiques
mysqldump -u root -p autolearn_db evenement equipe participation feedback > backup_tables.sql
```

#### Import par vos camarades

**Méthode 1: Via phpMyAdmin**
1. Ouvrir phpMyAdmin
2. Sélectionner la base de données
3. Cliquer sur "Importer"
4. Choisir le fichier `.sql`
5. Cliquer sur "Exécuter"

**Méthode 2: Via ligne de commande**
```bash
mysql -u root -p autolearn_db < backup_autolearn.sql
```

#### Partage du fichier SQL
- **Google Drive**: Créer un dossier partagé
- **Dropbox**: Lien de partage
- **WeTransfer**: Pour fichiers volumineux
- **Discord/Slack**: Si fichier < 8MB

---

### **Option 3: Docker avec Volume Partagé**

Créer un fichier `docker-compose.yml`:

```yaml
version: '3.8'
services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: autolearn_db
    ports:
      - "3306:3306"
    volumes:
      - ./docker/mysql-init:/docker-entrypoint-initdb.d
      - mysql-data:/var/lib/mysql

volumes:
  mysql-data:
```

Placer votre export SQL dans `docker/mysql-init/init.sql`

---

### **Option 4: Base de Données Partagée en Ligne**

#### Utiliser un serveur MySQL distant

**1. Créer une base de données gratuite:**
- **FreeMySQLHosting**: https://www.freemysqlhosting.net/
- **db4free**: https://www.db4free.net/
- **Clever Cloud**: https://www.clever-cloud.com/

**2. Configurer `.env.local`:**
```env
DATABASE_URL="mysql://username:password@host:3306/database_name?serverVersion=8.0"
```

**3. Partager les identifiants:**
- Créer un fichier `.env.shared` (NE PAS pusher sur Git)
- Partager via message privé

#### ⚠️ Attention
- Ne jamais pusher les identifiants sur Git
- Utiliser uniquement pour le développement
- Pas pour les données sensibles

---

## 📋 Workflow Recommandé pour Votre Équipe

### **Étape 1: Structure de Base (Une seule fois)**
```bash
# Personne 1 crée les migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Push sur Git
git add migrations/
git commit -m "Add database migrations"
git push
```

### **Étape 2: Données de Test (Fixtures)**
```bash
# Personne 1 crée les fixtures
# Éditer src/DataFixtures/AppFixtures.php

# Push sur Git
git add src/DataFixtures/
git commit -m "Add test data fixtures"
git push
```

### **Étape 3: Synchronisation par les Camarades**
```bash
# Camarades pullent le code
git pull

# Créent la base de données
php bin/console doctrine:database:create

# Exécutent les migrations
php bin/console doctrine:migrations:migrate

# Chargent les fixtures
php bin/console doctrine:fixtures:load
```

### **Étape 4: Mise à Jour des Données**
```bash
# Quand quelqu'un modifie les fixtures
git pull
php bin/console doctrine:fixtures:load --append  # Ajoute sans supprimer
# OU
php bin/console doctrine:fixtures:load  # Remplace tout
```

---

## 🔧 Commandes Utiles

### Réinitialiser complètement la base de données
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Vérifier l'état de la base de données
```bash
php bin/console doctrine:schema:validate
```

### Voir les migrations
```bash
php bin/console doctrine:migrations:list
```

---

## 📝 Exemple de Fixtures Complètes

Voir le fichier `src/DataFixtures/AppFixtures.php` pour un exemple complet avec:
- Événements
- Équipes
- Participations
- Feedbacks

---

## 🎯 Recommandation Finale

**Pour votre projet étudiant, utilisez:**

1. **Fixtures Doctrine** pour les données de test partagées
2. **Export SQL** pour partager ponctuellement des données spécifiques
3. **Git** uniquement pour le code (migrations + fixtures)

**Ne jamais pusher sur Git:**
- ❌ Fichiers de base de données (`.sql`, `.db`, `.sqlite`)
- ❌ Identifiants de connexion (`.env`, `.env.local`)
- ❌ Données sensibles ou personnelles

---

## 📞 Support

Si vous avez des questions, contactez votre équipe ou consultez:
- Documentation Symfony: https://symfony.com/doc/current/doctrine.html
- Documentation Fixtures: https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html
