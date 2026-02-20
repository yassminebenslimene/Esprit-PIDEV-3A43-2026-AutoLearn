# 🤖 Système de Suspension Automatique - Guide Complet

## 🎯 Fonctionnalité Métier Avancée

Ce système suspend automatiquement les étudiants inactifs depuis 7 jours ou plus et notifie à la fois l'étudiant ET tous les administrateurs.

---

## ✨ Fonctionnalités

### 1. Détection Automatique d'Inactivité
- ✅ Vérifie la dernière connexion de chaque étudiant
- ✅ Seuil configurable (par défaut: 7 jours)
- ✅ Ignore les comptes déjà suspendus
- ✅ Ignore les administrateurs

### 2. Suspension Automatique
- ✅ Suspend le compte automatiquement
- ✅ Enregistre la raison: "Compte inactif - Inactivité prolongée (suspension automatique)"
- ✅ Marque `suspendedBy` comme `null` (= système automatique)
- ✅ Bloque l'accès immédiatement

### 3. Notifications Email
- ✅ **Email à l'étudiant**: Notification de suspension avec raison
- ✅ **Email aux admins**: Notification avec détails complets (nom, email, jours d'inactivité)

### 4. Suivi de Connexion
- ✅ Enregistre `lastLoginAt` à chaque connexion
- ✅ Permet de calculer précisément la période d'inactivité

---

## 🗄️ Structure de la Base de Données

### Nouveau Champ Ajouté

| Champ | Type | Description |
|-------|------|-------------|
| `last_login_at` | DATETIME | Date et heure de la dernière connexion |

**Migration**: `Version20260220010531.php` ✅ Appliquée

---

## 🚀 Utilisation

### Option 1: Script Interactif (Recommandé)

```bash
.\run_auto_suspend.bat
```

Le script propose:
1. Mode SIMULATION (test sans modifications)
2. Mode RÉEL (suspensions effectives)
3. Personnaliser le nombre de jours
4. Quitter

### Option 2: Ligne de Commande

**Mode simulation** (aucune modification):
```bash
php bin/console app:auto-suspend-inactive-users --dry-run
```

**Mode réel** (suspensions effectives):
```bash
php bin/console app:auto-suspend-inactive-users
```

**Personnaliser le seuil** (ex: 14 jours):
```bash
php bin/console app:auto-suspend-inactive-users --days=14
```

**Simulation avec seuil personnalisé**:
```bash
php bin/console app:auto-suspend-inactive-users --days=14 --dry-run
```

---

## ⏰ Automatisation avec Cron/Tâche Planifiée

### Windows (Planificateur de Tâches)

1. **Ouvrir le Planificateur de tâches**:
   - Rechercher "Planificateur de tâches" dans Windows

2. **Créer une tâche de base**:
   - Nom: "AutoLearn - Suspension Automatique"
   - Description: "Suspend les étudiants inactifs depuis 7 jours"

3. **Déclencheur**:
   - Quotidien à 02:00 (2h du matin)

4. **Action**:
   - Programme: `C:\php\php.exe` (chemin vers PHP)
   - Arguments: `bin/console app:auto-suspend-inactive-users`
   - Dossier de démarrage: `C:\Users\hitec\OneDrive\Bureau\AutoLearn\autolearn`

### Linux/Mac (Crontab)

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (exécution quotidienne à 2h du matin)
0 2 * * * cd /path/to/autolearn && php bin/console app:auto-suspend-inactive-users >> /var/log/autolearn-suspend.log 2>&1
```

---

## 📧 Emails Envoyés

### Email à l'Étudiant

**Sujet**: Account Suspended - AutoLearn Platform

**Contenu**:
- Notification de suspension
- Raison: Inactivité prolongée (X jours)
- Contact support
- Design professionnel avec gradient rouge

**Template**: `templates/emails/suspension.html.twig`

### Email aux Administrateurs

**Sujet**: Suspension Automatique - Étudiant Inactif - AutoLearn

**Contenu**:
- Nom et email de l'étudiant
- Nombre de jours d'inactivité
- Date de suspension
- Actions effectuées
- Lien vers le backoffice

**Template**: `templates/emails/admin_inactive_notification.html.twig`

---

## 🔍 Exemple d'Exécution

```bash
$ php bin/console app:auto-suspend-inactive-users

Suspension Automatique des Utilisateurs Inactifs
=================================================

Recherche des étudiants inactifs depuis 7 jours ou plus...

2 étudiant(s) inactif(s) trouvé(s)
----------------------------------

- Jean Dupont (jean.dupont@example.com) - Inactif depuis 10 jours
✓ Jean Dupont suspendu

- Marie Martin (marie.martin@example.com) - Inactif depuis 15 jours
✓ Marie Martin suspendu

[OK] Terminé! 2 étudiant(s) suspendu(s), 0 erreur(s)
```

---

## 📊 Flux de Travail

### 1. Détection

```
Système vérifie tous les étudiants
    ↓
Filtre: role = ETUDIANT
    ↓
Filtre: isSuspended = false
    ↓
Filtre: lastLoginAt < (aujourd'hui - 7 jours)
    ↓
Liste des étudiants inactifs
```

### 2. Suspension

```
Pour chaque étudiant inactif:
    ↓
Marquer comme suspendu
    ↓
Enregistrer date et raison
    ↓
suspendedBy = null (système)
    ↓
Sauvegarder en base
```

### 3. Notifications

```
Envoyer email à l'étudiant
    ↓
Récupérer tous les admins
    ↓
Envoyer email à chaque admin
    ↓
Logger les résultats
```

---

## 🛠️ Fichiers Créés/Modifiés

### Nouveaux Fichiers (5)

1. **`src/Command/AutoSuspendInactiveUsersCommand.php`**
   - Commande Symfony pour la suspension automatique
   - Détection des utilisateurs inactifs
   - Envoi des notifications

2. **`templates/emails/admin_inactive_notification.html.twig`**
   - Template HTML pour email admin

3. **`templates/emails/admin_inactive_notification.txt.twig`**
   - Template texte pour email admin

4. **`migrations/Version20260220010531.php`**
   - Migration pour ajouter `last_login_at`

5. **`run_auto_suspend.bat`**
   - Script interactif pour exécuter la commande

### Fichiers Modifiés (3)

1. **`src/Entity/User.php`**
   - Ajout du champ `lastLoginAt`
   - Getter/Setter pour `lastLoginAt`

2. **`src/Security/AuthenticationSuccessHandler.php`**
   - Mise à jour de `lastLoginAt` à chaque connexion
   - Injection de `EntityManagerInterface`

3. **`src/Service/BrevoMailService.php`**
   - Ajout de `sendAdminNotificationInactiveSuspension()`

---

## ✅ Tests

### Test 1: Mode Simulation

```bash
php bin/console app:auto-suspend-inactive-users --dry-run
```

Vérifiez:
- ✅ Liste des étudiants inactifs affichée
- ✅ Aucune modification en base
- ✅ Message "MODE SIMULATION"

### Test 2: Mode Réel

```bash
php bin/console app:auto-suspend-inactive-users
```

Vérifiez:
- ✅ Étudiants suspendus en base
- ✅ Emails reçus par les étudiants
- ✅ Emails reçus par les admins
- ✅ Connexion bloquée pour les suspendus

### Test 3: Seuil Personnalisé

```bash
php bin/console app:auto-suspend-inactive-users --days=14 --dry-run
```

Vérifiez:
- ✅ Seuls les inactifs depuis 14+ jours sont listés

---

## 🎯 Avantages Métier

### 1. Gestion Automatisée
- ✅ Pas d'intervention manuelle requise
- ✅ Exécution quotidienne automatique
- ✅ Gain de temps pour les admins

### 2. Sécurité Renforcée
- ✅ Comptes inactifs désactivés automatiquement
- ✅ Réduction des risques de sécurité
- ✅ Conformité RGPD (gestion des comptes dormants)

### 3. Communication Transparente
- ✅ Étudiants informés de la suspension
- ✅ Admins notifiés de chaque action
- ✅ Traçabilité complète

### 4. Flexibilité
- ✅ Seuil configurable
- ✅ Mode simulation pour tests
- ✅ Réactivation possible à tout moment

---

## 🔐 Sécurité et Conformité

### RGPD
- ✅ Gestion des comptes inactifs (obligation légale)
- ✅ Notification des utilisateurs
- ✅ Possibilité de réactivation

### Audit Trail
- ✅ Date de suspension enregistrée
- ✅ Raison documentée
- ✅ `suspendedBy = null` indique suspension automatique
- ✅ Logs de la commande

---

## 📞 Support

### Commandes Utiles

**Voir l'aide**:
```bash
php bin/console app:auto-suspend-inactive-users --help
```

**Vérifier les utilisateurs inactifs**:
```bash
php bin/console app:auto-suspend-inactive-users --dry-run
```

**Logs**:
```bash
tail -f var/log/dev.log
```

### Dépannage

**Problème**: Aucun utilisateur détecté
**Solution**: Vérifiez que `lastLoginAt` est bien mis à jour à chaque connexion

**Problème**: Emails non envoyés
**Solution**: Vérifiez la configuration Brevo dans `.env`

**Problème**: Erreur de migration
**Solution**: 
```bash
php bin/console doctrine:migrations:migrate
```

---

## 🎉 Résultat Final

Un système **100% automatisé** qui:
- ✅ Détecte les étudiants inactifs
- ✅ Suspend automatiquement après 7 jours
- ✅ Notifie l'étudiant par email
- ✅ Notifie tous les admins par email
- ✅ S'exécute quotidiennement via cron
- ✅ Offre un mode simulation pour tests
- ✅ Permet la réactivation manuelle

**C'est une vraie fonctionnalité métier avancée!** 🚀
