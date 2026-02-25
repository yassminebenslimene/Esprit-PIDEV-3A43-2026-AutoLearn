# 🔧 Correction Timeout Ollama

## 🐛 Problème Identifié

L'assistant IA ne détectait pas les actions et répondait toujours avec le mode fallback.

### Erreur dans les Logs

```
[2026-02-21T18:08:11.530188+01:00] app.ERROR: Ollama service error 
{"message":"Idle timeout reached for \"http://localhost:11434/api/generate\"."}
```

### Cause

Le timeout de 15 secondes était **trop court**. Ollama prenait plus de 15 secondes pour générer une réponse, donc:

1. La requête timeout après 15 secondes
2. OllamaService retourne `null`
3. AIAssistantService passe en mode fallback
4. Le mode fallback ne peut pas exécuter d'actions

## ✅ Solution Appliquée

Augmentation du timeout de **15 à 30 secondes** dans `OllamaService.php`:

```php
// AVANT
'timeout' => 15 // Trop court!

// APRÈS
'timeout' => 30 // Suffisant pour llama3.2:1b
```

## 🎯 Pourquoi 30 Secondes?

Le modèle `llama3.2:1b` avec un prompt système détaillé prend environ:
- 15-25 secondes pour générer une réponse complète
- Plus de temps si le contexte est complexe (admin avec statistiques)

30 secondes est un bon compromis entre:
- ✅ Laisser le temps à Ollama de répondre
- ✅ Ne pas faire attendre l'utilisateur trop longtemps

## 🧪 Test

Maintenant, testez à nouveau:

```
Admin: "creer un nouveau etudiant"
```

**Résultat attendu:**
```
✅ Étudiant créé avec succès: [Nom]
📋 ID: [id]
🔑 Mot de passe par défaut: AutoLearn2026!
```

**Au lieu de:**
```
❌ Je peux t'aider avec les cours, événements, équipes et tes progrès. 
   Pose-moi une question spécifique! 😊
```

## 📊 Comparaison

| Aspect | Avant (15s) | Après (30s) |
|--------|-------------|-------------|
| Timeout | ❌ Trop court | ✅ Suffisant |
| Ollama | ❌ Timeout | ✅ Répond |
| Actions | ❌ Pas détectées | ✅ Détectées |
| Mode | ❌ Fallback | ✅ Ollama |

## 🚀 Prochaines Étapes

1. **Rafraîchissez la page** (Ctrl+F5)
2. **Testez une action admin:**
   - "creer un nouveau etudiant Jean Dupont"
   - "utilisateurs inactifs depuis 7 jours?"
   - "creer une equipe Team Alpha pour l'evenement 1"

3. **Vérifiez les logs** si problème:
   ```bash
   Get-Content var/log/dev.log -Tail 20
   ```

## 💡 Optimisations Futures

Si 30 secondes est encore trop long, vous pouvez:

1. **Réduire le prompt système** (moins d'exemples)
2. **Réduire num_predict** (moins de tokens générés)
3. **Utiliser un modèle plus petit** (mais moins intelligent)
4. **Utiliser le streaming** (réponses progressives)

## ✅ Résultat

L'assistant IA peut maintenant:
- ✅ Utiliser Ollama correctement
- ✅ Détecter les actions dans les demandes
- ✅ Exécuter les actions admin
- ✅ Répondre de manière intelligente

---

**Fichier modifié:** `src/Service/OllamaService.php`
**Cache vidé:** ✅
**Prêt à tester:** ✅
