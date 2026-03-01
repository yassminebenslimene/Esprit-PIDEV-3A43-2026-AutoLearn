# 🧪 Guide de Test - Génération d'Exercices par IA

**Date:** 1er mars 2026  
**Objectif:** Vérifier que l'IA génère des réponses complètes (150-300 caractères)

---

## ✅ Pré-requis

1. Le fix VARCHAR(30) → TEXT a été appliqué
2. La clé API Groq est configurée dans `.env`
3. Le serveur de développement est lancé

---

## 🎯 Test 1: Génération Simple

### Étapes
1. Ouvrir le navigateur: `http://localhost:8000/backoffice/exercice`
2. Cliquer sur "🤖 Générer avec IA"
3. Remplir le formulaire:
   - **Sujet:** Les variables en Python
   - **Niveau:** Débutant
   - **Nombre:** 3
4. Cliquer sur "🚀 Générer"

### Résultat Attendu
- ✅ Message: "3 exercice(s) généré(s) avec succès!"
- ✅ Page rechargée automatiquement
- ✅ 3 nouveaux exercices dans la liste

### Vérification
```sql
-- Vérifier les 3 derniers exercices
SELECT 
    id,
    LENGTH(question) as len_question,
    LENGTH(reponse) as len_reponse,
    question,
    reponse
FROM exercice
ORDER BY id DESC
LIMIT 3;
```

**Critères de succès:**
- ✅ len_question > 30 caractères
- ✅ len_reponse > 100 caractères (idéalement 150-300)
- ✅ Réponses complètes avec 3-5 phrases
- ✅ Pas de troncature

---

## 🎯 Test 2: Génération Niveau Avancé

### Étapes
1. Cliquer sur "🤖 Générer avec IA"
2. Remplir:
   - **Sujet:** Les design patterns en Java
   - **Niveau:** Avancé
   - **Nombre:** 2
3. Générer

### Résultat Attendu
- ✅ Réponses plus longues et techniques
- ✅ Vocabulaire avancé
- ✅ Points plus élevés (15-20)

---

## 🎯 Test 3: Vérification des Logs

### Commande
```bash
# Voir les logs de génération
tail -f var/log/dev.log | grep "exercise generation"
```

### Logs Attendus
```
[info] Generating 3 exercises for subject: Les variables en Python
[info] AI response received, parsing...
[info] Successfully parsed 3 exercises
[info] All exercises validated (length > 100 chars)
```

### Logs à Éviter
```
[warning] Skipping exercise with too short answer  ❌
[error] Failed to parse AI response  ❌
```

---

## 🎯 Test 4: Vérification Base de Données

### Script Batch
```bash
# Exécuter le script de test
test_ai_generation.bat
```

### Ou SQL Direct
```sql
-- Statistiques globales
SELECT 
    COUNT(*) as total_exercices,
    AVG(LENGTH(reponse)) as longueur_moyenne,
    MIN(LENGTH(reponse)) as longueur_min,
    MAX(LENGTH(reponse)) as longueur_max
FROM exercice;

-- Exercices avec réponses courtes (à éviter)
SELECT COUNT(*) as nb_reponses_courtes 
FROM exercice 
WHERE LENGTH(reponse) < 100;

-- Exercices avec réponses idéales
SELECT COUNT(*) as nb_reponses_ideales 
FROM exercice 
WHERE LENGTH(reponse) BETWEEN 150 AND 300;
```

### Résultats Attendus
- ✅ longueur_moyenne > 200 caractères
- ✅ longueur_min > 100 caractères
- ✅ nb_reponses_courtes = 0
- ✅ nb_reponses_ideales > 80% du total

---

## 🎯 Test 5: Test de Correction IA

### Étapes
1. Aller sur un challenge: `http://localhost:8000/challenge/1`
2. Démarrer le challenge
3. Répondre à une question avec une réponse sémantiquement correcte mais formulée différemment

**Exemple:**
- Question: "Qu'est-ce qu'une variable en Python?"
- Réponse attendue: "Une variable en Python est un conteneur qui permet de stocker une valeur en mémoire..."
- Votre réponse: "C'est un espace mémoire qui stocke des données sous un nom"

### Résultat Attendu
- ✅ L'IA reconnaît que le sens est correct
- ✅ Score: 90-100%
- ✅ Feedback positif
- ✅ Explication détaillée

---

## 📊 Critères de Validation Globaux

### Génération d'Exercices
| Critère | Valeur Attendue | Status |
|---------|-----------------|--------|
| Longueur min réponse | > 100 caractères | ⬜ |
| Longueur moyenne | 150-300 caractères | ⬜ |
| Nombre de phrases | 3-5 phrases | ⬜ |
| Taux de succès | > 95% | ⬜ |
| Temps de génération | < 10 secondes | ⬜ |

### Correction IA
| Critère | Valeur Attendue | Status |
|---------|-----------------|--------|
| Analyse sémantique | Fonctionne | ⬜ |
| Feedback détaillé | Présent | ⬜ |
| Explication claire | Présente | ⬜ |
| Conseils concrets | Présents | ⬜ |

---

## 🐛 Problèmes Possibles

### Problème 1: "Format de réponse invalide"
**Cause:** L'IA ajoute du texte avant/après le JSON  
**Solution:** Le système extrait automatiquement le JSON, réessayer

### Problème 2: "Réponses trop courtes"
**Cause:** L'IA génère des réponses < 100 caractères  
**Solution:** 
1. Vérifier que le fix VARCHAR(30) → TEXT est appliqué
2. Vérifier les logs pour voir la réponse brute
3. Réessayer avec un sujet plus précis

### Problème 3: "Service IA non disponible"
**Cause:** Clé API Groq invalide ou quota dépassé  
**Solution:**
1. Vérifier `.env`: `GROQ_API_KEY=gsk_...`
2. Tester: `php bin/console app:test-groq`
3. Régénérer la clé sur https://console.groq.com

### Problème 4: Timeout
**Cause:** Connexion lente ou serveur Groq surchargé  
**Solution:** Réessayer, le système a un retry automatique

---

## 📝 Checklist Finale

Avant de considérer le système comme validé:

- [ ] Test 1 réussi: Génération simple (3 exercices)
- [ ] Test 2 réussi: Génération avancée (2 exercices)
- [ ] Test 3 réussi: Logs sans erreurs
- [ ] Test 4 réussi: Statistiques conformes
- [ ] Test 5 réussi: Correction IA fonctionne
- [ ] Aucun exercice avec réponse < 100 caractères
- [ ] Longueur moyenne > 200 caractères
- [ ] Pas d'erreurs dans les logs

---

## 🎉 Validation Réussie

Si tous les tests passent:
- ✅ Le système de génération IA fonctionne correctement
- ✅ Les réponses sont complètes et détaillées
- ✅ La correction IA analyse le sens, pas la forme
- ✅ Le système est prêt pour la production

---

**Prochaine étape:** Générer un jeu de données de test avec différents sujets et niveaux pour valider la qualité globale.

**Commande rapide:**
```bash
# Test complet automatisé
test_ai_generation.bat
```
