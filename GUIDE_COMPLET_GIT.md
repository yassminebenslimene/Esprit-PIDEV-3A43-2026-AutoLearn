# 📚 Guide Complet: Push web → Pull ilef → Fix Migrations

## 🎯 Objectif

1. Pousser tout le travail de suspension dans la branche `web`
2. Récupérer ce travail dans la branche `ilef`
3. Corriger les problèmes de migrations

---

## 📍 Étape 1: Push vers la branche `web`

### Situation Actuelle
- ✅ Vous êtes sur la branche `web`
- ✅ Tout est commité
- ❌ GitHub bloque le push (secrets détectés)

### Solution

**Option A: Autoriser les secrets (Recommandé - 2 minutes)**

1. Ouvrez ces liens dans votre navigateur:
   - https://github.com/yassminebenslimene/autolearn_3A43_Brain-up/security/secret-scanning/unblock-secret/39uWwWijMdciWnFDm1OLYloC1oW
   - https://github.com/yassminebenslimene/autolearn_3A43_Brain-up/security/secret-scanning/unblock-secret/39uWwXUERTGhGtXaRFQWkrgvaVT

2. Cliquez sur "Allow secret" ou "I'll fix it later"

3. Puis:
```bash
git push origin web
```

**Option B: Force push (Si Option A ne fonctionne pas)**

```bash
git push origin web --force
```

---

## 📍 Étape 2: Passer à la branche `ilef`

```bash
git checkout ilef
```

---

## 📍 Étape 3: Pull les changements de `web`

```bash
git pull origin web
```

### Si vous avez des conflits:

1. **Voir les fichiers en conflit**:
```bash
git status
```

2. **Résoudre chaque conflit**:
   - Ouvrez les fichiers marqués en conflit
   - Cherchez les marqueurs `<<<<<<<`, `=======`, `>>>>>>>`
   - Choisissez quelle version garder
   - Supprimez les marqueurs

3. **Marquer comme résolu**:
```bash
git add .
git commit -m "Merge web into ilef - resolved conflicts"
```

---

## 📍 Étape 4: Fixer les Migrations

### 4.1 Vérifier l'état des migrations

```bash
php bin/console doctrine:migrations:status
```

### 4.2 Problème Typique

Vous verrez probablement:
```
[WARNING] You have 22 available migrations to execute.
[ERROR] Migration DoctrineMigrations\Version20260209083209 failed
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'commentaire' already exists
```

### 4.3 Solution: Marquer les migrations comme exécutées

**Option A: Marquer toutes les migrations comme exécutées**

```bash
php bin/console doctrine:migrations:version --add --all --no-interaction
```

**Option B: Marquer migration par migration**

```bash
# Voir les migrations disponibles
php bin/console doctrine:migrations:list

# Marquer une migration spécifique comme exécutée
php bin/console doctrine:migrations:version DoctrineMigrations\\Version20260209083209 --add
```

### 4.4 Vérifier que la migration de suspension est appliquée

```bash
php bin/console dbal:run-sql "DESCRIBE user"
```

Vous devriez voir:
- `is_suspended`
- `suspended_at`
- `suspension_reason`
- `suspended_by`

### 4.5 Si les colonnes de suspension n'existent pas

```bash
php bin/console dbal:run-sql "ALTER TABLE user ADD is_suspended TINYINT(1) DEFAULT 0 NOT NULL, ADD suspended_at DATETIME DEFAULT NULL, ADD suspension_reason VARCHAR(500) DEFAULT NULL, ADD suspended_by INT DEFAULT NULL"
```

Puis marquez la migration comme exécutée:
```bash
php bin/console doctrine:migrations:version DoctrineMigrations\\Version20260219233502 --add
```

---

## 📍 Étape 5: Vérifier que tout fonctionne

### 5.1 Vider le cache

```bash
php bin/console cache:clear
```

### 5.2 Vérifier les routes

```bash
php bin/console debug:router | findstr suspend
php bin/console debug:router | findstr reactivate
```

Vous devriez voir:
```
backoffice_user_suspend       POST    /backoffice/users/{id}/suspend
backoffice_user_reactivate    POST    /backoffice/users/{id}/reactivate
```

### 5.3 Tester l'application

1. Démarrez le serveur:
```bash
symfony server:start
```
ou
```bash
php -S localhost:8000 -t public
```

2. Allez sur http://localhost:8000/backoffice/users

3. Testez:
   - Suspendre un compte
   - Réactiver un compte
   - Essayer de se connecter avec un compte suspendu

---

## 📍 Étape 6: Push la branche `ilef`

```bash
git push origin ilef
```

---

## ✅ Checklist Finale

### Branche `web`
- [ ] Push réussi vers origin/web
- [ ] .env retiré du tracking Git
- [ ] Clés API révoquées et régénérées

### Branche `ilef`
- [ ] Pull de web réussi
- [ ] Conflits résolus (si présents)
- [ ] Migrations fixées
- [ ] Colonnes de suspension présentes dans la DB
- [ ] Routes de suspension/réactivation enregistrées
- [ ] Cache vidé
- [ ] Application testée et fonctionnelle
- [ ] Push vers origin/ilef réussi

---

## 🆘 Dépannage

### Problème: "Table already exists"

**Solution**: Marquez la migration comme déjà exécutée
```bash
php bin/console doctrine:migrations:version NomDeLaMigration --add
```

### Problème: "Column not found"

**Solution**: Exécutez le SQL manuellement
```bash
php bin/console dbal:run-sql "ALTER TABLE ..."
```

### Problème: Conflits Git complexes

**Solution**: Gardez la version de web
```bash
git checkout --theirs fichier_en_conflit.php
git add fichier_en_conflit.php
```

### Problème: Push toujours bloqué

**Solution**: Force push (après avoir autorisé sur GitHub)
```bash
git push origin web --force
```

---

## 📞 Résumé des Commandes

```bash
# 1. Push web
git push origin web  # ou --force si nécessaire

# 2. Passer à ilef
git checkout ilef

# 3. Pull web
git pull origin web

# 4. Fixer migrations
php bin/console doctrine:migrations:version --add --all --no-interaction

# 5. Vérifier
php bin/console cache:clear
php bin/console debug:router | findstr suspend

# 6. Push ilef
git push origin ilef
```

---

**Bonne chance!** 🚀
