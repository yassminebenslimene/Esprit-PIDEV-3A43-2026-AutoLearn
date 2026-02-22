# 🚀 Démarrage Rapide - Assistant IA

## ⚡ Installation en 5 Minutes

### Étape 1: Installer Ollama (2 min)
```bash
# Windows - Ouvrir PowerShell en admin
winget install Ollama.Ollama

# OU télécharger: https://ollama.com/download/windows
```

### Étape 2: Télécharger le Modèle (2 min)
```bash
# Ouvrir un nouveau terminal
ollama pull llama3.2:3b
```

### Étape 3: Vérifier (30 sec)
```bash
ollama list
# Vous devriez voir: llama3.2:3b
```

### Étape 4: Inclure le Widget (30 sec)
Ouvrir `templates/base.html.twig` et ajouter avant `</body>`:
```twig
{% include 'ai_assistant/chat_widget.html.twig' %}
```

### Étape 5: Tester! ✅
1. Démarrer Symfony: `symfony server:start`
2. Se connecter à la plateforme
3. Cliquer sur le bouton 🤖 en bas à droite
4. Poser une question!

## 🎯 Premières Questions à Tester

```
💬 "Quels cours pour débuter en Python?"
💬 "Événements cette semaine?"
💬 "Mon historique d'activités?"
💬 "Recommande-moi un cours"
```

## 🔧 Page de Test (Dev)

Visitez: `http://localhost:8000/ai-assistant/test`

Interface complète pour:
- Voir le statut du service
- Tester différentes questions
- Ajuster les paramètres
- Voir les temps de réponse

## ❓ Problèmes?

### "Ollama not available"
```bash
# Vérifier qu'Ollama tourne
# Windows: Chercher "Ollama" dans le gestionnaire des tâches
# Ou redémarrer l'application Ollama
```

### Réponses lentes?
```bash
# Utiliser un modèle plus léger
ollama pull llama3.2:1b
# Puis modifier .env: OLLAMA_MODEL=llama3.2:1b
```

## 📚 Documentation Complète

- **Architecture**: `ASSISTANT_IA_ARCHITECTURE.md`
- **Installation détaillée**: `GUIDE_INSTALLATION_IA.md`
- **Résumé complet**: `ASSISTANT_IA_RESUME.md`

## 🎉 C'est Tout!

Votre assistant IA est maintenant opérationnel! 🤖✨

---

**Besoin d'aide?** Consultez `GUIDE_INSTALLATION_IA.md` pour le dépannage complet.
