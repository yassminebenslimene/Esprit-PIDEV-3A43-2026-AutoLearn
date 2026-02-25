# ❌ Solution: "Failed to fetch"

## 🔍 Qu'est-ce que cette erreur?

L'erreur "Failed to fetch" signifie que le JavaScript dans le navigateur ne peut pas contacter l'API du serveur.

## 🎯 Solution en 3 étapes

### Étape 1: Vérifier que le serveur est démarré

```bash
symfony server:start
```

Tu devrais voir:
```
[OK] Web server listening on http://127.0.0.1:8000
```

### Étape 2: Tester avec la page de test

Ouvre cette URL dans ton navigateur:
```
http://localhost:8000/test-traduction.html
```

Cette page va te montrer exactement où est le problème.

### Étape 3: Vider le cache

```bash
php bin/console cache:clear
```

## 🧪 Test rapide

Ouvre une nouvelle fenêtre PowerShell et exécute:

```powershell
# Test 1: Le serveur répond-il?
curl http://localhost:8000

# Test 2: L'API des langues fonctionne-t-elle?
curl http://localhost:8000/api/languages

# Test 3: La traduction fonctionne-t-elle?
curl "http://localhost:8000/api/chapitres/1/translate?lang=en"
```

## ✅ Résultats attendus

### Test 1: Serveur
Tu devrais voir du HTML (la page d'accueil).

### Test 2: API des langues
```json
{
    "status": "success",
    "languages": {
        "fr": "Français",
        "en": "English",
        ...
    }
}
```

### Test 3: Traduction
```json
{
    "status": "success",
    "titre": "Variables and Data Types",
    "contenu": "...",
    "cached": true
}
```

## 🐛 Si ça ne marche toujours pas

### Problème A: Le serveur ne démarre pas

**Symptôme:** `symfony server:start` échoue

**Solution:**
```bash
# Utiliser PHP directement
php -S localhost:8000 -t public
```

### Problème B: Port 8000 déjà utilisé

**Symptôme:** "Address already in use"

**Solution:**
```bash
# Utiliser un autre port
symfony server:start --port=8001

# Puis tester sur
http://localhost:8001/test-traduction.html
```

### Problème C: Erreur 404 sur /api/languages

**Symptôme:** La route n'existe pas

**Solution:**
```bash
# Vérifier les routes
php bin/console debug:router | findstr api

# Vider le cache
php bin/console cache:clear

# Redémarrer le serveur
symfony server:stop
symfony server:start
```

### Problème D: Erreur 500

**Symptôme:** Erreur serveur interne

**Solution:**
```bash
# Voir les logs
Get-Content var/log/dev.log -Tail 50

# Vérifier que le service existe
php bin/console debug:container TranslationService
```

## 📋 Checklist de diagnostic

Coche chaque élément:

- [ ] Le serveur est démarré (`symfony server:start`)
- [ ] L'URL http://localhost:8000 est accessible
- [ ] La page de test http://localhost:8000/test-traduction.html s'affiche
- [ ] `curl http://localhost:8000/api/languages` retourne du JSON
- [ ] Le cache a été vidé (`php bin/console cache:clear`)
- [ ] La clé API est dans `.env.local`

## 🔧 Script de diagnostic automatique

Exécute ce script pour diagnostiquer automatiquement:

```powershell
powershell -ExecutionPolicy Bypass -File test-traduction-complet.ps1
```

Le script va tester:
- ✅ Serveur accessible
- ✅ API des langues
- ✅ Traduction en anglais
- ✅ Traduction en espagnol
- ✅ Gestion d'erreurs

## 💡 Astuce: Console du navigateur

Ouvre la console du navigateur (F12) et regarde les erreurs:

### Erreur réseau
```
Failed to fetch
```
→ Le serveur ne répond pas

### Erreur CORS
```
Access to fetch at '...' has been blocked by CORS policy
```
→ Problème de configuration (rare avec Symfony)

### Erreur 404
```
GET http://localhost:8000/api/chapitres/1/translate 404 (Not Found)
```
→ La route n'existe pas, vider le cache

### Erreur 500
```
GET http://localhost:8000/api/chapitres/1/translate 500 (Internal Server Error)
```
→ Erreur serveur, voir les logs

## 🎯 Solution garantie

Si rien ne fonctionne, cette séquence devrait résoudre le problème:

```bash
# 1. Arrêter le serveur
symfony server:stop

# 2. Vider le cache
php bin/console cache:clear

# 3. Vérifier la configuration
php bin/console debug:container TranslationService
php bin/console debug:router api_chapitre_translate

# 4. Redémarrer le serveur
symfony server:start

# 5. Tester
start http://localhost:8000/test-traduction.html
```

## 📞 Toujours bloqué?

1. **Vérifie les logs:**
```bash
Get-Content var/log/dev.log -Tail 100
```

2. **Teste l'API directement:**
```bash
curl http://localhost:8000/api/languages
```

3. **Vérifie que les fichiers existent:**
```bash
dir src\Service\TranslationService.php
dir src\Controller\TranslationController.php
```

4. **Consulte la documentation complète:**
- `DEPANNAGE_TRADUCTION.md`
- `SYSTEME_TRADUCTION_CHAPITRES.md`

## ✨ Une fois que ça marche

Tu verras:
1. Le sélecteur de langue en haut à droite
2. Un spinner pendant la traduction
3. Le contenu traduit s'affiche
4. Les traductions suivantes sont instantanées (cache)

---

**Besoin d'aide?** Exécute `test-traduction-complet.ps1` pour un diagnostic complet.
