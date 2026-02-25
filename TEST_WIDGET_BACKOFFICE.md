# 🧪 Test Rapide - Widget IA Backoffice

## ⚡ TEST EN 2 MINUTES

### 1. Vider le Cache
```bash
cd autolearn
php bin/console cache:clear
```

### 2. Tester la Navigation
1. Se connecter en tant qu'admin
2. Aller sur le **Dashboard** → ✅ Widget visible?
3. Aller sur **Utilisateurs** → ✅ Widget visible?
4. Cliquer sur un utilisateur → ✅ Widget visible?
5. Cliquer sur "Modifier" → ✅ Widget visible?
6. Aller sur **Cours** → ✅ Widget visible?
7. Aller sur **Événements** → ✅ Widget visible?
8. Aller sur **Analytics** → ✅ Widget visible?

### 3. Tester le Chat
1. Cliquer sur le bouton violet
2. ✅ Le chat s'ouvre?
3. Taper: "combien d'étudiants actifs?"
4. ✅ Réponse reçue?

## ✅ RÉSULTAT ATTENDU

Le widget doit être visible sur TOUTES les pages testées ci-dessus.

## ❌ SI LE WIDGET NE S'AFFICHE PAS

### Solution 1: Vider le Cache Navigateur
```
Ctrl + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)
```

### Solution 2: Vérifier la Console
```
F12 → Console
Vérifier s'il y a des erreurs JavaScript
```

### Solution 3: Vérifier que Vous Êtes Connecté
```
Le widget ne s'affiche que si vous êtes connecté
```

### Solution 4: Vérifier que Ce N'est Pas une Page Quiz
```
Le widget est automatiquement exclu des pages de quiz
```

## 🎯 PAGES OÙ LE WIDGET DOIT ÊTRE VISIBLE

### Gestion des Utilisateurs
- ✅ `/backoffice/users` (liste)
- ✅ `/backoffice/users/{id}` (détails)
- ✅ `/backoffice/users/{id}/edit` (modification)
- ✅ `/backoffice/users/new` (création)

### Gestion des Cours
- ✅ `/backoffice/cours` (liste)
- ✅ `/backoffice/cours/{id}` (détails)
- ✅ `/backoffice/cours/new` (création)
- ✅ `/backoffice/cours/{id}/edit` (modification)

### Gestion des Événements
- ✅ `/backoffice/evenement` (liste)
- ✅ `/backoffice/evenement/{id}` (détails)
- ✅ `/backoffice/evenement/new` (création)
- ✅ `/backoffice/evenement/{id}/edit` (modification)

### Autres Pages
- ✅ `/backoffice` (dashboard)
- ✅ `/backoffice/analytics` (analytics)
- ✅ `/backoffice/audit` (audit)
- ✅ `/backoffice/user-activity` (activité)
- ✅ `/backoffice/settings` (paramètres)

### Pages Exclues (Normal)
- ❌ `/backoffice/quiz/*` (toutes les pages de quiz)

## 🎉 VALIDATION

Si le widget est visible sur toutes les pages testées, la correction est réussie!

**Temps de test: 2 minutes ⏱️**
