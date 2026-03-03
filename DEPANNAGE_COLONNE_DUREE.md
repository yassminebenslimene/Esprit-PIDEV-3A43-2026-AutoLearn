# 🔧 DÉPANNAGE - Colonne 'duree' manquante

## ❌ Erreur
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 't0.duree' in 'field list'
```

---

## 🎯 SOLUTIONS (par ordre de priorité)

### ✅ SOLUTION 1 : Script automatique (RECOMMANDÉ)

```bash
.\REPARER_BDD.bat
```

Ce script essaie automatiquement plusieurs méthodes.

---

### ✅ SOLUTION 2 : Commande Doctrine

```bash
# Voir les changements
php bin/console doctrine:schema:update --dump-sql

# Appliquer
php bin/console doctrine:schema:update --force

# Vider le cache
php bin/console cache:clear
```

---

### ✅ SOLUTION 3 : SQL Manuel (si Doctrine ne fonctionne pas)

#### Via phpMyAdmin :
1. Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
2. Sélectionner la base `autolearn_db`
3. Onglet "SQL"
4. Copier-coller ce code :

```sql
ALTER TABLE cours ADD COLUMN duree INT NOT NULL DEFAULT 0;
ALTER TABLE challenge ADD COLUMN duree INT NOT NULL DEFAULT 0;
```

5. Cliquer sur "Exécuter"

#### Via MySQL CLI :
```bash
mysql -u root -p autolearn_db < ajouter-colonne-duree.sql
```

---

### ✅ SOLUTION 4 : Recréer la base (ATTENTION : perte de données)

```bash
# Supprimer la base
php bin/console doctrine:database:drop --force

# Recréer la base
php bin/console doctrine:database:create

# Créer toutes les tables
php bin/console doctrine:schema:create

# Charger les données de test (optionnel)
php bin/console doctrine:fixtures:load
```

---

## 🔍 DIAGNOSTIC

### Vérifier si la colonne existe :

```sql
-- Via phpMyAdmin ou MySQL CLI
SHOW COLUMNS FROM cours;
SHOW COLUMNS FROM challenge;
```

### Vérifier le schéma Doctrine :

```bash
php bin/console doctrine:schema:validate
```

Résultat attendu :
```
[Mapping]  OK - The mapping files are correct.
[Database] FAIL - The database schema is not in sync with the current mapping file.
```

---

## 📊 TABLES CONCERNÉES

### Table `cours`
Colonne manquante : `duree` (INT)

### Table `challenge`  
Colonne manquante : `duree` (INT)

---

## 🎯 APRÈS LA RÉPARATION

```bash
# 1. Vider le cache
php bin/console cache:clear

# 2. Valider le schéma
php bin/console doctrine:schema:validate

# 3. Redémarrer le serveur
symfony server:stop
symfony serve
```

---

## ⚠️ SI L'ERREUR PERSISTE

### Vérifier les entités PHP :

```bash
# Lister toutes les entités
php bin/console doctrine:mapping:info

# Vérifier une entité spécifique
php bin/console doctrine:mapping:describe "App\Entity\GestionDeCours\Cours"
```

### Vérifier la connexion à la base :

```bash
# Tester la connexion
php bin/console doctrine:query:sql "SELECT 1"

# Lister les tables
php bin/console doctrine:query:sql "SHOW TABLES"
```

---

## 🔄 WORKFLOW COMPLET

```bash
# 1. Vérifier le problème
php bin/console doctrine:schema:validate

# 2. Voir les changements nécessaires
php bin/console doctrine:schema:update --dump-sql

# 3. Appliquer les changements
php bin/console doctrine:schema:update --force

# 4. Vérifier que c'est résolu
php bin/console doctrine:schema:validate

# 5. Vider le cache
php bin/console cache:clear

# 6. Tester l'application
symfony serve
```

---

## 📝 SCRIPTS DISPONIBLES

1. **REPARER_BDD.bat** ⭐ - Réparation automatique
2. **ajouter-colonne-duree.sql** - Script SQL manuel
3. **MISE_A_JOUR_COMPLETE.bat** - Mise à jour complète
4. **MISE_A_JOUR_BDD.md** - Guide détaillé

---

## 💡 PRÉVENTION

Pour éviter ce problème à l'avenir :

### Après chaque modification d'entité :
```bash
php bin/console doctrine:schema:update --force
```

### Ou utiliser les migrations (RECOMMANDÉ) :
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 🎯 POUR TON AMI

Après `git pull`, toujours exécuter :
```bash
.\MISE_A_JOUR_COMPLETE.bat
```

Ce script met à jour automatiquement la base de données ! ✅

---

**Si le problème persiste après toutes ces solutions, contacte-moi avec :**
1. Le résultat de `php bin/console doctrine:schema:validate`
2. Le résultat de `SHOW COLUMNS FROM cours;`
3. La version de PHP et MySQL utilisée
