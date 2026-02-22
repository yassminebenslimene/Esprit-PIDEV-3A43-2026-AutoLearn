# 🧪 Testez l'Assistant IA Agent Actif - MAINTENANT

## ⚡ Tests Rapides (5 minutes)

### 🔧 Prérequis

1. **Ollama doit être installé et lancé**
   ```bash
   ollama --version
   # Si erreur, installez Ollama: https://ollama.ai/download
   ```

2. **Télécharger le modèle rapide**
   ```bash
   ollama pull llama3.2:1b
   ```
   ⏱️ Temps: ~1 minute (modèle léger)

3. **Serveur Symfony lancé**
   ```bash
   cd autolearn
   symfony server:start
   # Ou: php -S localhost:8000 -t public
   ```

## 🎯 Tests Admin (Actions)

### Test 1: Créer un Étudiant ⭐

1. Connectez-vous en tant qu'**ADMIN**
2. Ouvrez le chat IA (bulle en bas à droite)
3. Tapez:
   ```
   Crée-moi un nouvel étudiant Test User avec l'email test@autolearn.com
   ```

**✅ Résultat attendu:**
```
✅ Étudiant créé avec succès: Test User
📋 ID: 42
📧 Email: test@autolearn.com
🔑 Mot de passe par défaut: AutoLearn2026!
```

### Test 2: Utilisateurs Inactifs ⭐

Tapez:
```
Utilisateurs inactifs depuis 7 jours?
```

**✅ Résultat attendu:**
```
✅ 5 utilisateur(s) inactif(s) trouvé(s):
👥 [Liste des utilisateurs avec dates]
```

### Test 3: Créer une Équipe ⭐

Tapez:
```
Crée une équipe Team Test pour l'événement 1
```

**✅ Résultat attendu:**
```
✅ Équipe créée avec succès: Team Test
📋 ID: 15
🎯 Événement: [Nom de l'événement]
```

### Test 4: Statistiques Plateforme ⭐

Tapez:
```
Combien d'utilisateurs actifs?
```

**✅ Résultat attendu:**
```
[Nombre] utilisateurs actifs sur [total] au total. 
[X] suspendus, [Y] inactifs depuis 7 jours. 📊
```

## 🎓 Tests Étudiant (Intelligence)

### Test 5: Recommandation Intelligente ⭐

1. Connectez-vous en tant qu'**ÉTUDIANT** (niveau AVANCÉ)
2. Ouvrez le chat IA
3. Tapez:
   ```
   je suis doué en python, propose-moi des cours
   ```

**✅ Résultat attendu:**
```
🎓 Cours Python pour votre niveau AVANCÉ:
• Python Avancé (AVANCE, 40h, 15 chapitres)
• Machine Learning avec Python (EXPERT, 50h, 20 chapitres)
💡 Parfait pour votre niveau!
```

**❌ Résultat INCORRECT (ancien comportement):**
```
🎓 Nos cours disponibles:
• Python - Idéal pour débuter
• Java - Pour la programmation orientée objet
[Liste générique sans filtrage]
```

### Test 6: Équipes Disponibles ⭐

Tapez:
```
je veux participer à la conférence, il y a des équipes?
```

**✅ Résultat attendu:**
```
Pour Conférence IA: 3 équipe(s) (2 peuvent encore recruter).
Règle: 4-6 membres, 1 équipe/événement. 👥
```

**❌ Résultat INCORRECT (ancien comportement):**
```
Je n'ai pas accès aux informations sur les équipes.
```

### Test 7: Événements ⭐

Tapez:
```
Événements cette semaine?
```

**✅ Résultat attendu:**
```
2 événements: Hackaton IA (21/02, 39 places) et Conférence IA (28/02, 30 places). 🎫
```

### Test 8: Multilingue ⭐

Tapez en arabe:
```
مرحبا
```

**✅ Résultat attendu:**
```
مرحبا! كيف يمكنني مساعدتك؟ 😊
```

Tapez en anglais:
```
hello
```

**✅ Résultat attendu:**
```
Hi! How can I help you? 😊
```

## 🚀 Test de Vitesse

### Avant (llama3.2:3b)
- ⏱️ Temps de réponse: 3-5 secondes
- 📝 Tokens: 500
- ⏰ Timeout: 30 secondes

### Après (llama3.2:1b)
- ⚡ Temps de réponse: 1-2 secondes
- 📝 Tokens: 200
- ⏰ Timeout: 15 secondes

**Test:**
```
bnjr
```

Chronométrez le temps de réponse. Devrait être < 2 secondes.

## 🔍 Vérifications Techniques

### 1. Vérifier les Routes

```bash
cd autolearn
php bin/console debug:router | grep ai_assistant
```

**✅ Devrait afficher:**
```
ai_assistant_ask         POST   /ai-assistant/ask
ai_assistant_action      POST   /ai-assistant/action
ai_assistant_actions     GET    /ai-assistant/actions
ai_assistant_suggestions GET    /ai-assistant/suggestions
ai_assistant_status      GET    /ai-assistant/status
ai_assistant_test        GET    /ai-assistant/test
```

### 2. Vérifier les Services

```bash
php bin/console debug:container | grep -i action
```

**✅ Devrait afficher:**
```
App\Service\ActionExecutorService
```

### 3. Tester l'API Directement

**Lister les actions disponibles (Admin):**
```bash
curl -X GET http://localhost:8000/ai-assistant/actions \
  -H "Cookie: PHPSESSID=[votre_session_id]"
```

**✅ Résultat attendu:**
```json
{
  "success": true,
  "actions": {
    "public": {
      "get_popular_courses": "Voir les cours populaires"
    },
    "admin": {
      "create_student": "Créer un nouvel étudiant",
      "create_team": "Créer une nouvelle équipe",
      "suspend_user": "Suspendre un utilisateur",
      "unsuspend_user": "Réactiver un utilisateur",
      "get_inactive_users": "Lister les utilisateurs inactifs"
    }
  }
}
```

## 🐛 Dépannage

### Problème 1: "Ollama not available"

**Solution:**
```bash
# Vérifier qu'Ollama est lancé
curl http://localhost:11434/api/tags

# Si erreur, lancer Ollama
ollama serve
```

### Problème 2: "Modèle introuvable"

**Solution:**
```bash
# Télécharger le modèle
ollama pull llama3.2:1b

# Vérifier qu'il est installé
ollama list
```

### Problème 3: "Permission refusée"

**Cause:** Vous n'êtes pas admin

**Solution:**
- Connectez-vous avec un compte ADMIN
- Ou vérifiez la table `user` dans la BD:
  ```sql
  UPDATE user SET role = 'ADMIN' WHERE email = 'votre@email.com';
  ```

### Problème 4: Réponses lentes

**Solutions:**
1. Vérifier que vous utilisez `llama3.2:1b` (pas 3b)
2. Vérifier les ressources système (RAM, CPU)
3. Redémarrer Ollama:
   ```bash
   # Windows
   Ctrl+C dans le terminal Ollama
   ollama serve
   ```

### Problème 5: Actions ne s'exécutent pas

**Vérifications:**
1. Cache vidé?
   ```bash
   php bin/console cache:clear
   ```

2. Services enregistrés?
   ```bash
   php bin/console debug:container ActionExecutorService
   ```

3. Logs d'erreur?
   ```bash
   tail -f var/log/dev.log
   ```

## 📊 Checklist Complète

### Fonctionnalités

- [ ] ✅ Créer un étudiant (admin)
- [ ] ✅ Créer une équipe (admin)
- [ ] ✅ Lister utilisateurs inactifs (admin)
- [ ] ✅ Suspendre un utilisateur (admin)
- [ ] ✅ Statistiques plateforme (admin)
- [ ] ✅ Recommandation intelligente par niveau (étudiant)
- [ ] ✅ Recommandation intelligente par sujet (étudiant)
- [ ] ✅ Informations sur les équipes (étudiant)
- [ ] ✅ Événements à venir (étudiant)
- [ ] ✅ Multilingue (français, anglais, arabe)

### Performance

- [ ] ⚡ Réponses < 2 secondes
- [ ] 📝 Réponses concises (2-3 phrases)
- [ ] 🎯 Réponses contextuelles (pas de répétition)

### Sécurité

- [ ] 🔒 Actions admin bloquées pour étudiants
- [ ] ✅ Validation des paramètres
- [ ] 📋 Logging des actions

## 🎉 Si Tous les Tests Passent

**Félicitations!** 🎊

Votre assistant IA est maintenant:
- ✅ Intelligent (comprend le contexte)
- ✅ Rapide (< 2 secondes)
- ✅ Actif (peut créer, modifier, lister)
- ✅ Multilingue (français, anglais, arabe)
- ✅ Sécurisé (permissions respectées)

**L'assistant est prêt pour la production!** 🚀

## 📝 Rapport de Test

Après vos tests, notez:

```
✅ Tests réussis: [X/10]
❌ Tests échoués: [Y/10]
⏱️ Temps de réponse moyen: [X] secondes
💡 Observations:
- [Note 1]
- [Note 2]
- [Note 3]
```

---

**Besoin d'aide?** Consultez `IA_AGENT_ACTIF_COMPLET.md` pour plus de détails.
