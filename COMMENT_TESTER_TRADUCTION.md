# 🚀 Comment tester le système de traduction

## Étape 1: Démarrer le serveur

```bash
symfony server:start
```

Ou si tu n'as pas Symfony CLI:
```bash
php -S localhost:8000 -t public
```

## Étape 2: Choisir une méthode de test

### Méthode A: Page de test HTML (RECOMMANDÉ)

C'est la méthode la plus simple pour déboguer:

```
http://localhost:8000/test-traduction.html
```

Cette page te permet de:
- ✅ Tester l'API des langues
- ✅ Tester la traduction en plusieurs langues
- ✅ Voir les erreurs en temps réel
- ✅ Vérifier que le serveur répond

### Méthode B: Script PowerShell automatique

Exécute ce script pour tester tout automatiquement:

```powershell
powershell -ExecutionPolicy Bypass -File test-traduction-complet.ps1
```

Le script va:
- ✅ Vérifier que le serveur fonctionne
- ✅ Tester l'API des langues
- ✅ Tester plusieurs traductions
- ✅ Tester la gestion d'erreurs

### Méthode C: Interface utilisateur réelle

1. Ouvrir un chapitre:
```
http://localhost:8000/frontoffice/chapitre/1
```

2. Cliquer sur le sélecteur de langue en haut à droite

3. Sélectionner "Anglais" (ou une autre langue)

4. Observer la traduction

## Étape 3: Vérifier les résultats

### ✅ Résultat attendu

Quand tu sélectionnes une langue:
1. Un spinner de chargement apparaît
2. Le titre se traduit
3. Le contenu se traduit
4. Le formatage est préservé

### ❌ Si tu vois "Failed to fetch"

Cela signifie que le serveur ne répond pas. Solutions:

1. **Vérifier que le serveur est démarré:**
```bash
# Vérifier si le serveur tourne
curl http://localhost:8000
```

2. **Ouvrir la page de test:**
```
http://localhost:8000/test-traduction.html
```

3. **Vérifier les logs:**
```bash
# Voir les erreurs
Get-Content var/log/dev.log -Tail 50
```

4. **Vider le cache:**
```bash
php bin/console cache:clear
```

## Étape 4: Tests avancés

### Test API avec curl

```bash
# Test des langues
curl http://localhost:8000/api/languages

# Test de traduction
curl "http://localhost:8000/api/chapitres/1/translate?lang=en"
```

### Test dans la console du navigateur

Ouvre la console (F12) et exécute:

```javascript
// Test simple
fetch('/api/languages')
  .then(r => r.json())
  .then(d => console.log(d));

// Test de traduction
fetch('/api/chapitres/1/translate?lang=en')
  .then(r => r.json())
  .then(d => console.log(d));
```

## 🐛 Problèmes courants

### Problème 1: "Failed to fetch"
**Solution:** Le serveur n'est pas démarré
```bash
symfony server:start
```

### Problème 2: "Chapitre non trouvé"
**Solution:** Le chapitre n'existe pas, essayer avec un autre ID
```bash
# Voir les chapitres disponibles
php bin/console doctrine:query:sql "SELECT id, titre FROM chapitre LIMIT 10"
```

### Problème 3: Traduction très lente
**Solution:** Première traduction = appel API (2-5 sec). Les suivantes sont instantanées (cache).

### Problème 4: Erreur 500
**Solution:** Vérifier les logs
```bash
Get-Content var/log/dev.log -Tail 50
```

## 📋 Checklist rapide

Avant de tester, vérifie que:
- [ ] Le serveur est démarré (`symfony server:start`)
- [ ] L'URL http://localhost:8000 est accessible
- [ ] La clé API Groq est dans `.env.local`
- [ ] Le cache est vidé (`php bin/console cache:clear`)

## 🎯 Test rapide en 30 secondes

```bash
# 1. Démarrer le serveur
symfony server:start

# 2. Ouvrir la page de test
start http://localhost:8000/test-traduction.html

# 3. Cliquer sur "Tester /api/languages"
# 4. Cliquer sur "Anglais"
# 5. Vérifier que la traduction s'affiche
```

## 📚 Documentation complète

Pour plus de détails:
- `SYSTEME_TRADUCTION_CHAPITRES.md` - Documentation complète
- `DEPANNAGE_TRADUCTION.md` - Guide de dépannage
- `TEST_TRADUCTION_CHAPITRES.md` - Tests détaillés

---

**Besoin d'aide?** Consulte `DEPANNAGE_TRADUCTION.md`
