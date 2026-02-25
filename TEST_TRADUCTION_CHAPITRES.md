# 🧪 Guide de Test - Système de Traduction

## ✅ Prérequis

- ✅ Clé API Groq configurée dans `.env.local`
- ✅ Cache Symfony vidé
- ✅ Serveur Symfony démarré

## 🚀 Tests à effectuer

### Test 1: Traduction via l'interface utilisateur

1. **Démarrer le serveur**:
```bash
symfony server:start
```

2. **Ouvrir un chapitre**:
```
http://localhost:8000/frontoffice/chapitre/1
```

3. **Tester la traduction**:
   - Cliquer sur le sélecteur de langue (en haut à droite)
   - Sélectionner "English"
   - Observer l'indicateur de chargement
   - Vérifier que le titre ET le contenu sont traduits
   - Revenir à "Français" pour voir le contenu original

4. **Tester le cache**:
   - Sélectionner à nouveau "English"
   - La traduction doit être instantanée (depuis le cache)

### Test 2: API directe

**Test de traduction**:
```bash
# Windows PowerShell
Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/1/translate?lang=en" | Select-Object -ExpandProperty Content
```

**Résultat attendu**:
```json
{
    "status": "success",
    "titre": "Introduction to Programming",
    "contenu": "...",
    "cached": true
}
```

**Test des langues supportées**:
```bash
Invoke-WebRequest -Uri "http://localhost:8000/api/languages" | Select-Object -ExpandProperty Content
```

**Résultat attendu**:
```json
{
    "status": "success",
    "languages": {
        "fr": "Français",
        "en": "English",
        "es": "Español",
        "ar": "العربية",
        "de": "Deutsch",
        "it": "Italiano",
        "pt": "Português",
        "zh": "中文"
    }
}
```

### Test 3: Toutes les langues

Tester chaque langue dans l'interface:
- [ ] 🇫🇷 Français (original)
- [ ] 🇬🇧 English
- [ ] 🇪🇸 Español
- [ ] 🇸🇦 العربية
- [ ] 🇩🇪 Deutsch
- [ ] 🇮🇹 Italiano
- [ ] 🇵🇹 Português
- [ ] 🇨🇳 中文

### Test 4: Gestion d'erreurs

**Test avec chapitre inexistant**:
```bash
Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/99999/translate?lang=en" | Select-Object -ExpandProperty Content
```

**Résultat attendu**:
```json
{
    "status": "error",
    "message": "Chapitre non trouvé"
}
```

**Test avec langue non supportée**:
```bash
Invoke-WebRequest -Uri "http://localhost:8000/api/chapitres/1/translate?lang=xx" | Select-Object -ExpandProperty Content
```

**Résultat attendu**:
```json
{
    "status": "error",
    "message": "Langue non supportée: xx"
}
```

## 📊 Vérification des logs

Ouvrir les logs Symfony pour voir les appels API:
```bash
tail -f var/log/dev.log
```

Rechercher les lignes:
```
[info] Translating text {"target_lang":"en","source_lang":null,"text_length":1234}
[info] Translation successful
```

## 🎯 Checklist de validation

- [ ] Le sélecteur de langue s'affiche correctement
- [ ] L'indicateur de chargement apparaît pendant la traduction
- [ ] Le titre est traduit
- [ ] Le contenu est traduit
- [ ] Le formatage HTML est préservé
- [ ] Le retour au français fonctionne
- [ ] Le cache accélère les traductions répétées
- [ ] Les erreurs sont gérées gracieusement
- [ ] Les 8 langues fonctionnent
- [ ] L'API répond correctement

## 🐛 Problèmes courants

### Erreur: "Service not found"
**Solution**: Vider le cache
```bash
php bin/console cache:clear
```

### Erreur: "API key invalid"
**Solution**: Vérifier `.env.local`
```bash
type .env.local | findstr GROQ_API_KEY
```

### Traduction ne s'affiche pas
**Solution**: Ouvrir la console du navigateur (F12) et vérifier les erreurs JavaScript

### Timeout
**Solution**: Vérifier la connexion internet et les logs Symfony

## 📸 Captures d'écran attendues

1. **Avant traduction**: Contenu en français avec sélecteur de langue
2. **Pendant traduction**: Spinner de chargement
3. **Après traduction**: Contenu traduit en anglais
4. **Console réseau**: Requête AJAX réussie avec status 200

## ✨ Résultat final

Si tous les tests passent, le système de traduction est opérationnel et prêt pour la production !

---

**Date**: 2026-02-25  
**Testé par**: _________________  
**Statut**: ⬜ En cours | ⬜ Réussi | ⬜ Échec
