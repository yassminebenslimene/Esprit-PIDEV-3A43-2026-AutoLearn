# ✅ Branche ilef - Configuration Complète

## 🎉 Merge Réussi!

Tout le travail de `web` est maintenant dans `ilef`!

---

## ⚡ Configuration Rapide (5 minutes)

### Option 1: Script Automatique (Recommandé)

```bash
.\setup_ilef.bat
```

Le script va:
1. Créer `.env` depuis `.env.example`
2. Vous demander de configurer les clés API
3. Fixer les migrations automatiquement
4. Créer les colonnes de suspension si nécessaire
5. Vider le cache
6. Vérifier les routes

### Option 2: Manuel

```bash
# 1. Créer .env
copy .env.example .env
notepad .env  # Configurez vos clés API

# 2. Fixer migrations
php bin/console doctrine:migrations:version --add --all --no-interaction

# 3. Vider cache
php bin/console cache:clear

# 4. Vérifier routes
php bin/console debug:router | findstr suspend
```

---

## 🔑 Configuration .env Requise

Éditez `.env` et remplacez:

```env
# Votre clé API Brevo
BREVO_API_KEY=xkeysib-votre_cle_ici

# Email de la plateforme
MAIL_FROM_EMAIL=autolearn66@gmail.com

# Clé SMTP Brevo
MAILER_DSN=smtp://apikey:votre_cle_smtp@smtp-relay.brevo.com:587

# Secret Symfony (chaîne aléatoire)
APP_SECRET=une_chaine_aleatoire_longue
```

---

## 🧪 Tests

```bash
# Démarrer le serveur
symfony server:start

# Tester
1. http://localhost:8000/backoffice/users
2. Suspendre un compte
3. Essayer de se connecter (devrait être bloqué)
4. Réactiver le compte
5. Se connecter (devrait fonctionner)
```

---

## 📤 Push vers GitHub

```bash
git push origin ilef
```

---

## 📚 Documentation Complète

- **FINALISATION_ILEF.md** - Guide détaillé complet
- **SUSPENSION_SYSTEM_GUIDE.md** - Guide du système de suspension
- **QUICK_START_SUSPENSION.md** - Guide rapide d'utilisation

---

## ✅ Checklist

- [ ] `.env` configuré avec vraies clés API
- [ ] Migrations fixées
- [ ] Cache vidé
- [ ] Routes vérifiées
- [ ] Application testée
- [ ] Push vers GitHub

---

**Tout est prêt! Suivez les étapes ci-dessus.** 🚀
