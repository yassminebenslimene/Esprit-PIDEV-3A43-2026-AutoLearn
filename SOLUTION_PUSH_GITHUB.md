# 🔧 Solution: Push Bloqué par GitHub

## 🎯 Solution Recommandée (2 minutes)

### Étape 1: Autoriser les secrets sur GitHub

Ouvrez ces deux liens dans votre navigateur:

1. **Clé API Brevo**:
   ```
   https://github.com/yassminebenslimene/autolearn_3A43_Brain-up/security/secret-scanning/unblock-secret/39uWwWijMdciWnFDm1OLYloC1oW
   ```

2. **Clé SMTP Brevo**:
   ```
   https://github.com/yassminebenslimene/autolearn_3A43_Brain-up/security/secret-scanning/unblock-secret/39uWwXUERTGhGtXaRFQWkrgvaVT
   ```

Sur chaque page, cliquez sur **"Allow secret"** ou **"I'll fix it later"**

### Étape 2: Push vers GitHub

```bash
git push origin web
```

✅ **C'est tout!** Le push devrait maintenant fonctionner.

---

## 🔐 Après le Push (IMPORTANT)

### 1. Révoquer les anciennes clés

1. Allez sur https://app.brevo.com
2. Connectez-vous
3. Allez dans **Settings > API Keys**
4. **Supprimez** les anciennes clés qui ont été exposées

### 2. Générer de nouvelles clés

1. Dans Brevo, cliquez sur **"Generate a new API key"**
2. Copiez la nouvelle clé

### 3. Mettre à jour votre .env local

```env
BREVO_API_KEY=votre_nouvelle_clé_ici
BREVO_SMTP_KEY=votre_nouvelle_clé_smtp_ici
```

### 4. Vérifier que .env est ignoré

```bash
git status
```

Le fichier `.env` ne doit PAS apparaître dans la liste.

---

## 📋 Checklist Complète

- [ ] Autoriser les secrets sur GitHub (liens ci-dessus)
- [ ] Push vers origin/web
- [ ] Révoquer les anciennes clés Brevo
- [ ] Générer de nouvelles clés
- [ ] Mettre à jour .env local
- [ ] Vérifier que .env est dans .gitignore
- [ ] Tester que l'application fonctionne avec les nouvelles clés

---

## 🚀 Ensuite: Pull dans la branche ilef

Une fois le push réussi, passez à la branche `ilef`:

```bash
# 1. Aller sur la branche ilef
git checkout ilef

# 2. Pull les changements de web
git pull origin web

# 3. Résoudre les conflits si nécessaire

# 4. Fixer les migrations
php bin/console doctrine:migrations:status
php bin/console doctrine:migrations:migrate
```

---

## ❓ Si ça ne fonctionne toujours pas

Si GitHub continue de bloquer après avoir autorisé les secrets, utilisez:

```bash
git push origin web --force
```

⚠️ **Attention**: `--force` réécrit l'historique. Assurez-vous que personne d'autre ne travaille sur la branche.

---

**Bonne chance!** 🎉
