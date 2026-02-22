# 🧪 Guide de Test - Suspension Automatique

## 🎯 Objectif

Tester le système de suspension automatique en simulant des étudiants inactifs.

---

## ⚡ Test Rapide (Automatique)

### Option 1: Script Batch (Recommandé)

```bash
.\test_auto_suspend.bat
```

Le script va:
1. Simuler 10 jours d'inactivité pour un étudiant
2. Afficher les étudiants inactifs (mode simulation)
3. Demander confirmation pour suspension réelle
4. Vérifier la suspension en base de données
5. Vous demander de vérifier les emails

### Option 2: Commandes Manuelles

```bash
# 1. Simuler l'inactivité (10 jours pour 1 étudiant)
php bin/console app:simulate-inactivity --days=10 --count=1

# 2. Vérifier les inactifs (mode simulation)
php bin/console app:auto-suspend-inactive-users --dry-run

# 3. Exécuter la suspension
php bin/console app:auto-suspend-inactive-users

# 4. Vérifier en base de données
php bin/console dbal:run-sql "SELECT userId, nom, prenom, email, is_suspended, suspended_at, suspension_reason FROM user WHERE role = 'ETUDIANT' AND is_suspended = 1"
```

---

## 📋 Test Détaillé Étape par Étape

### Étape 1: Vider le cache

```bash
php bin/console cache:clear
```

### Étape 2: Vérifier l'état initial

```bash
# Voir tous les étudiants
php bin/console dbal:run-sql "SELECT userId, nom, prenom, email, last_login_at, is_suspended FROM user WHERE role = 'ETUDIANT'"
```

### Étape 3: Simuler l'inactivité

**Option A: Pour 1 étudiant (10 jours)**
```bash
php bin/console app:simulate-inactivity
```

**Option B: Pour 2 étudiants (15 jours)**
```bash
php bin/console app:simulate-inactivity --days=15 --count=2
```

**Option C: SQL direct**
```bash
php bin/console dbal:run-sql "UPDATE user SET last_login_at = DATE_SUB(NOW(), INTERVAL 10 DAY) WHERE role = 'ETUDIANT' LIMIT 1"
```

### Étape 4: Vérifier les dates modifiées

```bash
php bin/console dbal:run-sql "SELECT userId, nom, prenom, email, last_login_at, DATEDIFF(NOW(), last_login_at) as jours_inactif FROM user WHERE role = 'ETUDIANT'"
```

Vous devriez voir des étudiants avec `jours_inactif >= 7`

### Étape 5: Test en mode simulation

```bash
php bin/console app:auto-suspend-inactive-users --dry-run
```

**Vérifiez**:
- ✅ Message "MODE SIMULATION"
- ✅ Liste des étudiants inactifs affichée
- ✅ Nombre de jours d'inactivité correct
- ✅ Message "X étudiant(s) seraient suspendus"

### Étape 6: Exécution réelle

```bash
php bin/console app:auto-suspend-inactive-users
```

**Vérifiez**:
- ✅ Message "✓ [Nom] suspendu" pour chaque étudiant
- ✅ Message "Terminé! X étudiant(s) suspendu(s)"

### Étape 7: Vérifier en base de données

```bash
php bin/console dbal:run-sql "SELECT userId, nom, prenom, email, is_suspended, suspended_at, suspension_reason, suspended_by FROM user WHERE role = 'ETUDIANT' AND is_suspended = 1"
```

**Vérifiez**:
- ✅ `is_suspended = 1`
- ✅ `suspended_at` = date/heure actuelle
- ✅ `suspension_reason` = "Compte inactif - Inactivité prolongée (suspension automatique)"
- ✅ `suspended_by = NULL` (système automatique)

### Étape 8: Vérifier les emails

**Email Étudiant**:
- ✅ Sujet: "Account Suspended - AutoLearn Platform"
- ✅ Contenu: Notification de suspension avec raison
- ✅ Design professionnel avec gradient rouge

**Email Admins**:
- ✅ Sujet: "Suspension Automatique - Étudiant Inactif - AutoLearn"
- ✅ Contenu: Nom, email, jours d'inactivité
- ✅ Design professionnel avec gradient violet

### Étape 9: Tester le blocage de connexion

1. Allez sur http://localhost:8000/login
2. Connectez-vous avec le compte suspendu
3. **Vérifiez**:
   - ✅ Redirection vers la page de login
   - ✅ Message d'erreur: "Votre compte a été suspendu. Raison: Compte inactif..."
   - ✅ Connexion bloquée

### Étape 10: Tester la réactivation

1. Allez sur http://localhost:8000/backoffice/users
2. Trouvez l'étudiant suspendu (badge rouge "Suspendu")
3. Cliquez sur "Réactiver"
4. Confirmez
5. **Vérifiez**:
   - ✅ Message de succès
   - ✅ Email de réactivation reçu
   - ✅ Connexion possible à nouveau

---

## 🔄 Réinitialiser pour Retester

### Réactiver tous les étudiants suspendus

```bash
php bin/console dbal:run-sql "UPDATE user SET is_suspended = 0, suspended_at = NULL, suspension_reason = NULL, suspended_by = NULL WHERE role = 'ETUDIANT'"
```

### Réinitialiser les dates de connexion

```bash
php bin/console dbal:run-sql "UPDATE user SET last_login_at = NOW() WHERE role = 'ETUDIANT'"
```

---

## 📊 Scénarios de Test

### Scénario 1: Étudiant inactif 10 jours

```bash
# Simuler
php bin/console app:simulate-inactivity --days=10 --count=1

# Vérifier
php bin/console app:auto-suspend-inactive-users --dry-run

# Suspendre
php bin/console app:auto-suspend-inactive-users
```

**Résultat attendu**: ✅ Suspendu (10 > 7 jours)

### Scénario 2: Étudiant inactif 5 jours

```bash
# Simuler
php bin/console app:simulate-inactivity --days=5 --count=1

# Vérifier
php bin/console app:auto-suspend-inactive-users --dry-run
```

**Résultat attendu**: ❌ Pas suspendu (5 < 7 jours)

### Scénario 3: Plusieurs étudiants inactifs

```bash
# Simuler 3 étudiants avec différentes périodes
php bin/console dbal:run-sql "UPDATE user SET last_login_at = DATE_SUB(NOW(), INTERVAL 8 DAY) WHERE role = 'ETUDIANT' LIMIT 1"
php bin/console dbal:run-sql "UPDATE user SET last_login_at = DATE_SUB(NOW(), INTERVAL 15 DAY) WHERE role = 'ETUDIANT' AND userId != (SELECT userId FROM (SELECT userId FROM user WHERE role = 'ETUDIANT' LIMIT 1) as t) LIMIT 1"

# Suspendre
php bin/console app:auto-suspend-inactive-users
```

**Résultat attendu**: ✅ Tous suspendus, emails envoyés à chacun + admins

### Scénario 4: Seuil personnalisé (14 jours)

```bash
# Simuler 10 jours
php bin/console app:simulate-inactivity --days=10

# Vérifier avec seuil 14 jours
php bin/console app:auto-suspend-inactive-users --days=14 --dry-run
```

**Résultat attendu**: ❌ Pas suspendu (10 < 14 jours)

---

## ✅ Checklist de Test

### Fonctionnalités de Base
- [ ] Commande `app:simulate-inactivity` fonctionne
- [ ] Commande `app:auto-suspend-inactive-users` fonctionne
- [ ] Mode `--dry-run` ne modifie rien
- [ ] Détection des étudiants inactifs correcte

### Suspension
- [ ] Étudiant suspendu en base de données
- [ ] `is_suspended = 1`
- [ ] `suspended_at` enregistré
- [ ] `suspension_reason` correct
- [ ] `suspended_by = NULL` (système)

### Emails
- [ ] Email envoyé à l'étudiant
- [ ] Email envoyé à tous les admins
- [ ] Contenu des emails correct
- [ ] Design professionnel

### Blocage de Connexion
- [ ] Connexion bloquée pour compte suspendu
- [ ] Message d'erreur affiché sur page login
- [ ] Redirection vers login

### Réactivation
- [ ] Bouton "Réactiver" visible
- [ ] Réactivation fonctionne
- [ ] Email de réactivation envoyé
- [ ] Connexion possible après réactivation

### Options
- [ ] Option `--days=X` fonctionne
- [ ] Option `--count=X` fonctionne (simulate-inactivity)
- [ ] Seuil personnalisé respecté

---

## 🆘 Dépannage

### Problème: Aucun étudiant détecté

**Vérification**:
```bash
php bin/console dbal:run-sql "SELECT userId, nom, prenom, last_login_at, DATEDIFF(NOW(), last_login_at) as jours FROM user WHERE role = 'ETUDIANT'"
```

**Solution**: Assurez-vous que `last_login_at` est bien dans le passé (> 7 jours)

### Problème: Emails non reçus

**Vérification**:
```bash
# Vérifier les logs
tail -f var/log/dev.log
```

**Solution**: Vérifiez la configuration Brevo dans `.env`

### Problème: Erreur "Column 'last_login_at' not found"

**Solution**:
```bash
php bin/console doctrine:migrations:migrate
```

### Problème: Connexion non bloquée

**Vérification**:
```bash
php bin/console dbal:run-sql "SELECT userId, email, is_suspended FROM user WHERE email = 'email@example.com'"
```

**Solution**: Videz le cache et reconnectez-vous
```bash
php bin/console cache:clear
```

---

## 🎉 Résultat Attendu

Après tous les tests, vous devriez avoir:
- ✅ Étudiants inactifs détectés automatiquement
- ✅ Suspensions effectuées
- ✅ Emails reçus (étudiants + admins)
- ✅ Connexions bloquées
- ✅ Réactivations fonctionnelles

**Le système fonctionne parfaitement!** 🚀
