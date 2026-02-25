# 🔧 Dépannage - Système de Traduction

## ❌ Erreur: "Failed to fetch"

Cette erreur signifie que le navigateur ne peut pas contacter l'API. Voici les solutions :

### Solution 1: Vérifier que le serveur est démarré

```bash
# Démarrer le serveur Symfony
symfony server:start

# Ou avec PHP
php -S localhost:8000 -t public
```

Le serveur doit être accessible sur `http://localhost:8000`

### Solution 2: Tester l'API directement

**Option A: Page de test HTML**
```
http://localhost:8000/test-traduction.html
```

Cette page permet de tester l'API sans passer par le template Twig.

**Option B: Commande curl**
```bash
# Test des langues
curl http://localhost:8000/api/languages

# Test de traduction
curl "http://localhost:8000/api/chapitres/1/translate?lang=en"
```

**Option C: Script batch**
```bash
# Exécuter le script de test
test-api-simple.bat
```

### Solution 3: Vérifier les logs

```bash
# Voir les logs en temps réel
tail -f var/log/dev.log

# Ou sur Windows
Get-Content var/log/dev.log -Wait -Tail 50
```

Rechercher les erreurs liées à `TranslationService` ou `TranslationController`.

### Solution 4: Vérifier la configuration

**Vérifier que le service est bien configuré:**
```bash
php bin/console debug:container TranslationService
```

**Vérifier que la route existe:**
```bash
php bin/console debug:router api_chapitre_translate
```

**Résultat attendu:**
```
Route Name   | api_chapitre_translate
Path         | /api/chapitres/{id}/translate
Method       | GET
```

### Solution 5: Vérifier la clé API Groq

```bash
# Windows
type .env.local | findstr GROQ_API_KEY

# Résultat attendu:
# GROQ_API_KEY=gsk_...
```

Si la clé est manquante ou invalide, la traduction échouera.

### Solution 6: Tester avec un chapitre spécifique

Ouvrir la console du navigateur (F12) et exécuter:

```javascript
// Test simple
fetch('/api/languages')
  .then(r => r.json())
  .then(d => console.log(d))
  .catch(e => console.error(e));

// Test de traduction
fetch('/api/chapitres/1/translate?lang=en')
  .then(r => r.json())
  .then(d => console.log(d))
  .catch(e => console.error(e));
```

## 🐛 Autres erreurs courantes

### Erreur: "Chapitre non trouvé"

**Cause:** Le chapitre avec cet ID n'existe pas dans la base de données.

**Solution:** Vérifier les chapitres disponibles:
```bash
php bin/console doctrine:query:sql "SELECT id, titre FROM chapitre LIMIT 10"
```

### Erreur: "Langue non supportée"

**Cause:** Le code de langue n'est pas dans la liste supportée.

**Solution:** Utiliser un des codes suivants:
- `fr` (Français)
- `en` (English)
- `es` (Español)
- `ar` (العربية)
- `de` (Deutsch)
- `it` (Italiano)
- `pt` (Português)
- `zh` (中文)

### Erreur: "Service not found"

**Cause:** Le cache Symfony n'est pas à jour.

**Solution:**
```bash
php bin/console cache:clear
```

### Erreur: Timeout ou traduction très lente

**Cause:** L'API Groq ne répond pas ou est surchargée.

**Solutions:**
1. Vérifier la connexion internet
2. Vérifier que la clé API est valide
3. Réessayer plus tard
4. Vérifier les logs: `var/log/dev.log`

## 📊 Checklist de diagnostic

- [ ] Le serveur Symfony est démarré (`symfony server:start`)
- [ ] L'URL `http://localhost:8000` est accessible
- [ ] La page de test `http://localhost:8000/test-traduction.html` fonctionne
- [ ] La route `/api/languages` retourne une réponse JSON
- [ ] La clé API Groq est présente dans `.env.local`
- [ ] Le cache a été vidé (`php bin/console cache:clear`)
- [ ] Les logs ne montrent pas d'erreurs (`var/log/dev.log`)
- [ ] La base de données contient des chapitres

## 🔍 Commandes de diagnostic

```bash
# 1. Vérifier que Symfony fonctionne
php bin/console about

# 2. Vérifier les routes
php bin/console debug:router | findstr translate

# 3. Vérifier les services
php bin/console debug:container Translation

# 4. Vérifier la base de données
php bin/console doctrine:query:sql "SELECT COUNT(*) FROM chapitre"

# 5. Tester l'API
curl http://localhost:8000/api/languages
```

## 💡 Solution rapide

Si rien ne fonctionne, essayer cette séquence:

```bash
# 1. Vider le cache
php bin/console cache:clear

# 2. Redémarrer le serveur
symfony server:stop
symfony server:start

# 3. Ouvrir la page de test
start http://localhost:8000/test-traduction.html

# 4. Tester l'API
curl http://localhost:8000/api/languages
```

## 📞 Support

Si le problème persiste:
1. Consulter les logs: `var/log/dev.log`
2. Vérifier la console du navigateur (F12)
3. Tester avec la page `test-traduction.html`
4. Vérifier que tous les fichiers sont bien créés:
   - `src/Service/TranslationService.php`
   - `src/Controller/TranslationController.php`
   - `config/services.yaml` (contient TranslationService)

---

**Dernière mise à jour:** 2026-02-25
