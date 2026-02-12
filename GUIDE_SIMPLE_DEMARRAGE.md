# 🚀 Guide Simple de Démarrage

## ✅ Solution Simple: Utiliser le serveur Symfony (Port 8001)

J'ai créé des fichiers simples pour démarrer et arrêter le serveur.

---

## 🎯 Démarrage en 2 clics

### Étape 1: Démarrer le serveur

**Double-cliquez** sur: `start-server.bat`

Une fenêtre s'ouvrira et le serveur démarrera automatiquement.

### Étape 2: Ouvrir votre navigateur

Allez sur: `http://127.0.0.1:8001/backoffice/evenement/`

**C'est tout!** ✅

---

## 🌐 URLs à utiliser

```
Backoffice:     http://127.0.0.1:8001/backoffice
Login:          http://127.0.0.1:8001/login
Événements:     http://127.0.0.1:8001/backoffice/evenement/
Équipes:        http://127.0.0.1:8001/backoffice/equipe/
Participations: http://127.0.0.1:8001/backoffice/participation/
Frontoffice:    http://127.0.0.1:8001/
```

---

## 🛑 Arrêter le serveur

**Double-cliquez** sur: `stop-server.bat`

---

## 🔑 Connexion

**Important:** Vous devez être connecté en tant qu'Admin pour accéder aux formulaires.

1. Allez sur: `http://127.0.0.1:8001/login`
2. Connectez-vous avec un compte Admin
3. Vous verrez le menu avec "Événements", "Équipes", "Participations"

---

## ✅ Avantages de cette solution

- ✅ Pas de configuration compliquée
- ✅ Pas de problème de router
- ✅ Pas de problème de CSS
- ✅ Le serveur Symfony gère tout automatiquement
- ✅ Fonctionne en arrière-plan
- ✅ Démarrage et arrêt en 1 clic

---

## 📝 Fichiers créés

- `start-server.bat` → Démarre le serveur
- `stop-server.bat` → Arrête le serveur

---

## 🎯 Test rapide

1. Double-cliquez sur `start-server.bat`
2. Ouvrez votre navigateur
3. Allez sur `http://127.0.0.1:8001/login`
4. Connectez-vous
5. Allez sur `http://127.0.0.1:8001/backoffice/evenement/new`
6. Vous verrez le formulaire complet!

---

## 🐛 Si le serveur ne démarre pas

Ouvrez un terminal et tapez:

```bash
# Arrêter tous les serveurs
symfony server:stop

# Tuer les processus PHP
taskkill /F /IM php.exe /T

# Redémarrer
symfony server:start -d
```

---

## ✅ Résumé

**Utilisez le port 8001** - C'est plus simple et ça fonctionne parfaitement!

Toutes vos URLs:
- Remplacez `localhost:8000` par `127.0.0.1:8001`
- Ou utilisez `localhost:8001`

**Exemple:**
- ❌ `http://localhost:8000/backoffice/evenement/`
- ✅ `http://127.0.0.1:8001/backoffice/evenement/`

---

**Faites maintenant:** Double-cliquez sur `start-server.bat` et testez! 🎉
