# 🚀 Guide Rapide - Système de Suspension

## ⚡ Démarrage Rapide (2 minutes)

### 1️⃣ Accéder à la Liste des Utilisateurs
```
Backoffice > Users
```

### 2️⃣ Suspendre un Étudiant
1. Trouver l'étudiant dans la liste
2. Cliquer sur le bouton **🟠 Suspendre**
3. Choisir une raison dans le menu déroulant
4. Cliquer sur **Confirmer la suspension**
5. ✅ L'étudiant reçoit un email automatiquement

### 3️⃣ Réactiver un Étudiant
1. Trouver l'étudiant avec le badge **🔴 Suspendu**
2. Cliquer sur le bouton **🟢 Réactiver**
3. Confirmer l'action
4. ✅ L'étudiant reçoit un email de réactivation

---

## 🎯 Raisons de Suspension Disponibles

1. **Compte inactif - Inactivité prolongée**
2. **Violation des règles de la plateforme**
3. **Comportement inapproprié envers d'autres utilisateurs**
4. **Activité suspecte détectée sur le compte**
5. **Non-paiement ou problème de facturation**
6. **Demande de suspension par l'étudiant**
7. **Vérification d'identité requise**
8. **Suspension temporaire pour enquête**

---

## 🔍 Identifier un Compte Suspendu

Dans la liste des utilisateurs, regardez la colonne **Status**:
- 🟢 **Actif** = Compte normal
- 🔴 **Suspendu** = Compte suspendu

---

## 📧 Emails Automatiques

### Suspension
L'étudiant reçoit un email contenant:
- Notification de suspension
- Raison détaillée
- Contact support (autolearn66@gmail.com)

### Réactivation
L'étudiant reçoit un email contenant:
- Confirmation de réactivation
- Lien de connexion direct
- Message de bienvenue

---

## 🔐 Ce qui se passe pour l'Étudiant Suspendu

1. **Connexion bloquée** - Ne peut plus se connecter
2. **Déconnexion automatique** - Si déjà connecté, déconnecté immédiatement
3. **Message d'erreur** - Voit la raison de la suspension
4. **Email reçu** - Notification avec détails

---

## ⚠️ Règles Importantes

- ✅ Seuls les **étudiants** peuvent être suspendus
- ❌ Les **admins** ne peuvent pas être suspendus
- ❌ Vous ne pouvez pas suspendre **votre propre compte**
- ✅ La suspension est **réversible** (réactivation possible)
- ✅ Toutes les **données sont préservées**

---

## 🛠️ Dépannage

### L'email n'est pas envoyé?
Vérifiez la configuration Brevo dans `.env`:
```env
BREVO_API_KEY=votre_clé_api
MAIL_FROM_EMAIL=autolearn66@gmail.com
```

### Le bouton ne fonctionne pas?
1. Vérifiez que vous êtes connecté en tant qu'admin
2. Actualisez la page (F5)
3. Videz le cache: `php bin/console cache:clear`

### L'étudiant peut toujours se connecter?
1. Vérifiez le badge dans la liste (doit être rouge "Suspendu")
2. L'étudiant doit se déconnecter et reconnecter
3. Le système le bloquera automatiquement

---

## 📊 Statistiques

Le système enregistre automatiquement:
- 📅 Date et heure de suspension
- 📝 Raison de la suspension
- 👤 Admin qui a effectué l'action
- ✅ État actuel du compte

---

## 🎓 Avantages vs Suppression

| Suppression | Suspension |
|-------------|------------|
| ❌ Définitif | ✅ Réversible |
| ❌ Perte de données | ✅ Données préservées |
| ❌ Pas de notification | ✅ Email automatique |
| ❌ Pas d'historique | ✅ Audit complet |

---

## 📞 Support

Besoin d'aide?
- 📧 Email: autolearn66@gmail.com
- 📖 Guide complet: `SUSPENSION_SYSTEM_GUIDE.md`
- 📝 Détails techniques: `WHAT_I_DID_SUSPENSION.md`

---

**C'est tout! Le système est prêt à l'emploi.** 🎉
