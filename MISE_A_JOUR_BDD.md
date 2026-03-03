# 🗄️ MISE À JOUR DE LA BASE DE DONNÉES

## ❌ Erreur rencontrée
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 't0.duree' in 'field list'
```

## 🎯 Cause
Le schéma de la base de données n'est pas synchronisé avec les entités PHP.
Une colonne `duree` est attendue mais n'existe pas dans la table.

---

## ✅ SOLUTION 1 : Mise à jour automatique (RECOMMANDÉ)

```bash
php bin/console doctrine:schema:update --force
```

Cette commande :
- Compare les entités PHP avec la base de données
- Crée les colonnes manquantes
- Ajoute les tables manquantes
- Met à jour les index

---

## ✅ SOLUTION 2 : Voir les changements avant d'appliquer

```bash
# Voir les requêtes SQL qui seront exécutées
php bin/console doctrine:schema:update --dump-sql

# Si tout est OK, appliquer
php bin/console doctrine:schema:update --force
```

---

## ✅ SOLUTION 3 : Utiliser les migrations (PROPRE)

```bash
# Créer une migration
php bin/console make:migration

# Vérifier le fichier de migration créé dans migrations/

# Appliquer la migration
php bin/console doctrine:migrations:migrate
```

---

## 🚀 COMMANDES COMPLÈTES

```bash
# 1. Mettre à jour le schéma
php bin/console doctrine:schema:update --force

# 2. Vider le cache
php bin/console cache:clear

# 3. Redémarrer le serveur
symfony server:stop
symfony serve
```

---

## 📊 TABLES CONCERNÉES

La colonne `duree` peut être dans :
- Table `cours` (durée du cours)
- Table `chapitre` (durée du chapitre)
- Table `quiz` (durée du quiz)
- Table `evenement` (durée de l'événement)
- Table `challenge` (durée du challenge)

---

## ⚠️ ATTENTION

Si tu as des données importantes en production :
1. Fais un backup de la base de données
2. Utilise les migrations au lieu de `schema:update`

```bash
# Backup MySQL
mysqldump -u root -p autolearn_db > backup.sql

# Ou via phpMyAdmin : Exporter
```

---

## 🎯 POUR TON AMI

Après `git pull`, exécuter :
```bash
composer install
php bin/console doctrine:schema:update --force
php bin/console cache:clear
```

---

## 📝 SCRIPT AUTOMATIQUE

Voir `MISE_A_JOUR_COMPLETE.bat`
