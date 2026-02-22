# 🔒 Système de Suspension de Comptes - Guide Complet

## Vue d'ensemble

Le système de suspension remplace la suppression définitive des comptes étudiants. C'est une approche professionnelle qui permet de:
- **Préserver les données** pour audit et conformité
- **Réactiver les comptes** si nécessaire
- **Maintenir un historique** des actions administratives
- **Notifier automatiquement** les utilisateurs par email

---

## ✨ Fonctionnalités Implémentées

### 1. Suspension de Compte
- ✅ Bouton "Suspendre" pour chaque étudiant actif
- ✅ Modal avec sélection de raison de suspension
- ✅ 6 raisons prédéfinies disponibles
- ✅ Email automatique envoyé à l'étudiant
- ✅ Enregistrement de la date et de l'admin responsable

### 2. Réactivation de Compte
- ✅ Bouton "Réactiver" pour chaque étudiant suspendu
- ✅ Email de confirmation envoyé à l'étudiant
- ✅ Restauration complète de l'accès

### 3. Blocage de Connexion
- ✅ Les utilisateurs suspendus ne peuvent pas se connecter
- ✅ Déconnexion automatique si suspension pendant la session
- ✅ Message d'erreur avec la raison de suspension

### 4. Interface Utilisateur
- ✅ Badge "Suspendu" (rouge) ou "Actif" (vert) dans la colonne Status
- ✅ Boutons colorés: Suspendre (orange), Réactiver (vert)
- ✅ Modal élégant pour la suspension
- ✅ Confirmations pour toutes les actions

### 5. Notifications Email
- ✅ Email de suspension avec raison détaillée
- ✅ Email de réactivation avec lien de connexion
- ✅ Design professionnel avec templates HTML et texte

---

## 🗄️ Structure de la Base de Données

### Nouveaux champs dans la table `user`:

| Champ | Type | Description |
|-------|------|-------------|
| `is_suspended` | BOOLEAN | État de suspension (0 = actif, 1 = suspendu) |
| `suspended_at` | DATETIME | Date et heure de la suspension |
| `suspension_reason` | VARCHAR(500) | Raison de la suspension |
| `suspended_by` | INT | ID de l'admin qui a suspendu |

---

## 📋 Raisons de Suspension Disponibles

1. **Compte inactif - Inactivité prolongée** (par défaut)
2. **Violation des règles de la plateforme**
3. **Comportement inapproprié envers d'autres utilisateurs**
4. **Activité suspecte détectée sur le compte**
5. **Non-paiement ou problème de facturation**
6. **Demande de suspension par l'étudiant**
7. **Vérification d'identité requise**
8. **Suspension temporaire pour enquête**

---

## 🔄 Flux de Travail

### Suspendre un Étudiant

1. Admin clique sur "Suspendre" dans la liste des utilisateurs
2. Modal s'ouvre avec sélection de raison
3. Admin confirme la suspension
4. Système:
   - Marque le compte comme suspendu
   - Enregistre la date, raison et admin responsable
   - Envoie un email à l'étudiant
   - Affiche un message de confirmation
5. L'étudiant ne peut plus se connecter

### Réactiver un Étudiant

1. Admin clique sur "Réactiver" pour un compte suspendu
2. Confirmation demandée
3. Système:
   - Réactive le compte
   - Efface les données de suspension
   - Envoie un email de réactivation
   - Affiche un message de confirmation
4. L'étudiant peut à nouveau se connecter

---

## 📧 Templates Email

### Email de Suspension
- **Fichier HTML**: `templates/emails/suspension.html.twig`
- **Fichier Texte**: `templates/emails/suspension.txt.twig`
- **Contenu**: Notification de suspension avec raison et contact support

### Email de Réactivation
- **Fichier HTML**: `templates/emails/reactivation.html.twig`
- **Fichier Texte**: `templates/emails/reactivation.txt.twig`
- **Contenu**: Confirmation de réactivation avec lien de connexion

---

## 🛠️ Fichiers Modifiés/Créés

### Entité
- ✅ `src/Entity/User.php` - Ajout des champs de suspension

### Contrôleur
- ✅ `src/Controller/BackofficeController.php` - Routes suspend/reactivate

### Service
- ✅ `src/Service/BrevoMailService.php` - Méthodes d'envoi d'emails

### Event Subscriber
- ✅ `src/EventSubscriber/CheckSuspendedUserSubscriber.php` - Blocage de connexion

### Templates
- ✅ `templates/backoffice/users/users.html.twig` - UI mise à jour
- ✅ `templates/emails/suspension.html.twig` - Email suspension
- ✅ `templates/emails/suspension.txt.twig` - Email suspension (texte)
- ✅ `templates/emails/reactivation.html.twig` - Email réactivation
- ✅ `templates/emails/reactivation.txt.twig` - Email réactivation (texte)

### Migration
- ✅ `migrations/Version20260219233502.php` - Ajout des colonnes

---

## 🔐 Sécurité

### Protection CSRF
- Tous les formulaires utilisent des tokens CSRF
- Tokens générés dynamiquement pour chaque utilisateur

### Contrôle d'Accès
- Seuls les admins peuvent suspendre/réactiver
- Impossible de suspendre un admin
- Impossible de suspendre son propre compte

### Audit Trail
- Date de suspension enregistrée
- Admin responsable enregistré
- Raison de suspension conservée

---

## 🎯 Avantages par rapport à la Suppression

| Suppression | Suspension |
|-------------|------------|
| ❌ Perte définitive des données | ✅ Données préservées |
| ❌ Impossible de revenir en arrière | ✅ Réactivation possible |
| ❌ Pas d'historique | ✅ Historique complet |
| ❌ Pas de notification | ✅ Emails automatiques |
| ❌ Problèmes de conformité | ✅ Conforme RGPD |

---

## 📊 Statistiques et Reporting

Le système permet de:
- Voir le nombre d'utilisateurs suspendus
- Identifier les raisons de suspension les plus fréquentes
- Suivre l'historique des suspensions/réactivations
- Générer des rapports d'audit

---

## 🚀 Utilisation

### Pour Suspendre un Compte:
1. Allez dans **Backoffice > Users**
2. Trouvez l'étudiant à suspendre
3. Cliquez sur **Suspendre** (bouton orange)
4. Sélectionnez une raison
5. Confirmez

### Pour Réactiver un Compte:
1. Allez dans **Backoffice > Users**
2. Trouvez l'étudiant suspendu (badge rouge "Suspendu")
3. Cliquez sur **Réactiver** (bouton vert)
4. Confirmez

---

## 🔧 Configuration

### Variables d'Environnement Requises:
```env
BREVO_API_KEY=your_api_key_here
MAIL_FROM_EMAIL=autolearn66@gmail.com
MAIL_FROM_NAME=AutoLearn
```

### Services Symfony:
Le système utilise:
- `BrevoMailService` pour les emails
- `CheckSuspendedUserSubscriber` pour le blocage de connexion
- Doctrine ORM pour la persistance

---

## 📝 Notes Importantes

1. **Les admins ne peuvent pas être suspendus** - Seuls les étudiants peuvent être suspendus
2. **Pas d'auto-suspension** - Un admin ne peut pas suspendre son propre compte
3. **Emails requis** - Le système Brevo doit être configuré pour les notifications
4. **Déconnexion automatique** - Les utilisateurs suspendus sont déconnectés immédiatement

---

## 🎓 Métier Avancé / API

Ce système représente une **fonctionnalité métier avancée** car il:

1. **Gestion du cycle de vie utilisateur** - Suspension/réactivation professionnelle
2. **Audit et conformité** - Traçabilité complète des actions
3. **Notifications automatisées** - Intégration API Brevo
4. **Sécurité renforcée** - Event subscribers et contrôles d'accès
5. **Expérience utilisateur** - Interface moderne avec modals et feedback

---

## 📞 Support

Pour toute question ou problème:
- Email: autolearn66@gmail.com
- Consultez les logs Symfony pour le débogage
- Vérifiez la configuration Brevo pour les emails

---

**Système développé avec ❤️ pour AutoLearn Platform**
