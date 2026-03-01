# Guide de Dépannage - Génération d'Exercices par IA

## Erreur: "Format de réponse invalide"

### Causes possibles:

1. **L'IA ajoute du texte avant/après le JSON**
   - Exemple: "Voici les exercices: {...}"
   - Solution: Le système extrait maintenant automatiquement le JSON

2. **JSON mal formé**
   - Virgule manquante, guillemets mal fermés, etc.
   - Solution: Retry automatique (2 tentatives)

3. **Réponses trop courtes**
   - L'IA génère des réponses de moins de 20 caractères
   - Solution: Validation automatique et rejet des réponses courtes

4. **Clé API Groq invalide ou expirée**
   - Solution: Vérifier `.env` et régénérer la clé sur https://console.groq.com

5. **Problème de connexion internet**
   - Solution: Vérifier la connexion

### Solutions implémentées:

✅ **Retry automatique** - 2 tentatives avec pause de 1 seconde
✅ **Extraction intelligente du JSON** - Regex pour extraire le JSON même avec du texte autour
✅ **Validation des réponses** - Rejet des réponses < 20 caractères
✅ **Messages d'erreur détaillés** - Indiquent la cause probable
✅ **Logging amélioré** - Pour déboguer les problèmes

---

## Comment tester

### Test 1: Génération simple
```
Sujet: Les variables en Python
Niveau: Débutant
Nombre: 3
```

**Résultat attendu:** 3 exercices avec réponses complètes (2-3 phrases minimum)

### Test 2: Sujet complexe
```
Sujet: Les design patterns en Java
Niveau: Avancé
Nombre: 5
```

**Résultat attendu:** 5 exercices techniques avec réponses détaillées

### Test 3: Vérifier les logs
Si erreur, vérifier les logs dans `var/log/dev.log`:
```bash
tail -f var/log/dev.log | grep "exercise generation"
```

---

## Vérification de la configuration

### 1. Vérifier la clé API Groq

Fichier `.env`:
```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxxx
GROQ_API_URL=https://api.groq.com/openai/v1/chat/completions
GROQ_MODEL=llama-3.3-70b-versatile
```

### 2. Tester la connexion Groq

```bash
php bin/console app:test-groq
```

Si erreur, régénérer la clé sur https://console.groq.com

### 3. Vérifier les permissions

```bash
# Windows
icacls var/log /grant Users:F

# Linux/Mac
chmod -R 777 var/log
```

---

## Messages d'erreur et solutions

### "Le service de génération IA n'est pas disponible"
**Cause:** Groq API non accessible
**Solution:** 
1. Vérifier la clé API dans `.env`
2. Vérifier la connexion internet
3. Vérifier que l'URL Groq est correcte

### "Aucune réponse de l'IA"
**Cause:** Timeout ou problème réseau
**Solution:**
1. Réessayer (retry automatique activé)
2. Vérifier la connexion internet
3. Vérifier les quotas Groq (limite gratuite: 30 req/min)

### "Format de réponse invalide"
**Cause:** JSON mal formé ou réponses trop courtes
**Solution:**
1. Le système réessaie automatiquement
2. Si échec persistant, essayer un sujet plus simple
3. Vérifier les logs pour voir la réponse brute

### "Réponses trop courtes"
**Cause:** L'IA génère des réponses < 20 caractères
**Solution:**
1. Le système les rejette automatiquement
2. Retry automatique avec prompt amélioré
3. Si échec, essayer un sujet plus précis

---

## Paramètres IA

### ExerciceGeneratorAIService

```php
'temperature' => 0.8,  // Créativité (0.0-1.0)
'max_tokens' => 3000,  // Longueur max réponse
```

**Ajuster si nécessaire:**
- Temperature trop basse (< 0.5) → Réponses répétitives
- Temperature trop haute (> 0.9) → Réponses incohérentes
- Max tokens trop bas (< 2000) → Réponses tronquées

---

## Exemples de réponses valides

### ✅ BON (accepté)
```json
{
    "exercices": [
        {
            "question": "Qu'est-ce qu'une variable en Python?",
            "reponse": "Une variable en Python est un conteneur qui permet de stocker une valeur en mémoire. Elle est créée automatiquement lors de la première affectation avec l'opérateur =. Python est un langage à typage dynamique, ce qui signifie que le type de la variable est déterminé automatiquement selon la valeur assignée.",
            "points": 8
        }
    ]
}
```

### ❌ MAUVAIS (rejeté)
```json
{
    "exercices": [
        {
            "question": "Qu'est-ce qu'une variable?",
            "reponse": "Un conteneur",  // ❌ Trop court (< 20 chars)
            "points": 8
        }
    ]
}
```

---

## Monitoring

### Vérifier les statistiques

```bash
# Nombre d'exercices générés aujourd'hui
php bin/console dbal:run-sql "SELECT COUNT(*) FROM exercice WHERE DATE(created_at) = CURDATE()"

# Logs d'erreurs IA
grep "exercise generation" var/log/dev.log | grep ERROR
```

---

## Support

Si le problème persiste après avoir suivi ce guide:

1. Vérifier les logs: `var/log/dev.log`
2. Tester avec un sujet simple: "Les variables"
3. Vérifier la clé API Groq
4. Vérifier les quotas Groq (limite gratuite)

---

**Dernière mise à jour:** 1er mars 2026
