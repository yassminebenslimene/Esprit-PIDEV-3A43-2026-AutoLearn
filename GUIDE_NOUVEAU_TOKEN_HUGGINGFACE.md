# 🔑 GUIDE: Créer un Nouveau Token Hugging Face

## ⚠️ PROBLÈME IDENTIFIÉ

L'API Hugging Face a changé en 2025:
- ❌ Ancienne API: `api-inference.huggingface.co` (ne fonctionne plus - erreur 410)
- ✅ Nouvelle API: `router.huggingface.co` (Inference Providers)

Ton token actuel:
- ❌ N'a pas les bonnes permissions (erreur 401)
- ❌ Ou est invalide/expiré

## 📋 ÉTAPES POUR CRÉER UN NOUVEAU TOKEN

### 1. Aller sur Hugging Face

Va sur: https://huggingface.co/settings/tokens

### 2. Créer un Nouveau Token

1. Clique sur **"Create new token"**
2. Nom du token: `autolearn-ai-reports`
3. Type: **Fine-grained** (pas "Read" ou "Write")

### 3. Sélectionner les Permissions

⚠️ **IMPORTANT**: Coche cette permission:

```
✅ Make calls to Inference Providers
```

Cette permission est OBLIGATOIRE pour utiliser la nouvelle API `router.huggingface.co`.

### 4. Créer et Copier le Token

1. Clique sur **"Create token"**
2. **COPIE IMMÉDIATEMENT** le token (il commence par `hf_`)
3. Tu ne pourras plus le voir après!

### 5. Mettre à Jour .env.local

Ouvre le fichier `.env.local` et remplace l'ancien token:

```env
###> huggingface ###
HUGGINGFACE_API_KEY=hf_NOUVEAU_TOKEN_ICI
HUGGINGFACE_MODEL=mistralai/Mistral-7B-Instruct-v0.2
###< huggingface ###
```

### 6. Vider le Cache Symfony

```bash
php bin/console cache:clear
```

### 7. Tester

Retourne sur le dashboard admin:
- http://localhost:8000/backoffice/evenement
- Clique sur "📊 Générer Rapport d'Analyse"
- Attends 30-60 secondes
- Le rapport devrait s'afficher!

## 🔍 VÉRIFICATION

Pour vérifier que ton nouveau token fonctionne:

```bash
php test_token_validity.php
```

Tu devrais voir:
```
✅ Token valide!
Utilisateur: [ton nom]
```

## 📝 NOTES IMPORTANTES

1. **Gratuit**: L'API Inference Providers a un tier gratuit généreux
2. **Modèle**: Mistral-7B-Instruct-v0.2 est gratuit
3. **Sécurité**: Ne JAMAIS commiter .env.local sur Git (déjà dans .gitignore)
4. **Expiration**: Les tokens n'expirent pas sauf si tu les révoque

## ❓ PROBLÈMES COURANTS

### Token invalide (401)
- Vérifie que tu as copié le token complet
- Vérifie qu'il commence par `hf_`
- Vérifie la permission "Make calls to Inference Providers"

### Timeout (60 secondes)
- Normal la première fois (le modèle se charge)
- Réessaye une 2ème fois

### Pas de réponse
- Vérifie que le cache est vidé: `php bin/console cache:clear`
- Redémarre le serveur: `php bin/console server:stop` puis `php bin/console server:start`

## ✅ CODE MIS À JOUR

Le code a été mis à jour pour utiliser:
- ✅ Nouvelle URL: `https://router.huggingface.co/v1/chat/completions`
- ✅ Format OpenAI-compatible
- ✅ Meilleurs messages d'erreur
- ✅ Logs détaillés pour debugging

Une fois le nouveau token configuré, tout devrait fonctionner! 🚀
