# 🤖 Assistant IA AutoLearn - README

## 🎯 Vue d'Ensemble

Un assistant intelligent avec RAG (Retrieval-Augmented Generation) qui facilite l'utilisation de la plateforme AutoLearn en utilisant:
- **Modèle IA**: Llama 3.2 (3B) - Open source, gratuit, local
- **RAG**: Accès direct à la base de données pour contexte en temps réel
- **Multilingue**: FR, EN, AR, ES
- **Interface**: Widget chat moderne et responsive

## 📁 Fichiers Créés

### Services Backend (PHP/Symfony)
```
src/Service/
├── OllamaService.php          # Communication avec Ollama
├── RAGService.php             # Collecte contexte BD
└── AIAssistantService.php     # Orchestration IA + RAG
```

### Contrôleur API
```
src/Controller/
└── AIAssistantController.php  # Routes API (/ai-assistant/*)
```

### Templates
```
templates/ai_assistant/
├── chat_widget.html.twig      # Widget chat flottant
└── test.html.twig             # Page de test (dev)
```

### Documentation
```
├── ASSISTANT_IA_ARCHITECTURE.md   # Architecture complète
├── GUIDE_INSTALLATION_IA.md       # Installation détaillée
├── ASSISTANT_IA_RESUME.md         # Résumé complet
├── DEMARRAGE_RAPIDE_IA.md         # Quick start (5 min)
├── PROMPT_SYSTEM_IA.md            # Prompts système
└── README_ASSISTANT_IA.md         # Ce fichier
```

## 🚀 Installation Rapide (5 Minutes)

### 1. Installer Ollama
```bash
# Windows
winget install Ollama.Ollama
```

### 2. Télécharger le Modèle
```bash
ollama pull llama3.2:3b
```

### 3. Inclure le Widget
Dans `templates/base.html.twig`, avant `</body>`:
```twig
{% include 'ai_assistant/chat_widget.html.twig' %}
```

### 4. Tester
```
http://localhost:8000/ai-assistant/test
```

## 🎯 Fonctionnalités

### Pour les Étudiants
- ✅ Recommandation de cours personnalisés
- ✅ Découverte d'événements avec météo
- ✅ Suivi des progrès et activités
- ✅ Navigation assistée sur la plateforme

### Pour les Administrateurs
- ✅ Statistiques en temps réel
- ✅ Gestion des utilisateurs inactifs
- ✅ Analyse des tendances
- ✅ Actions de gestion suggérées

## 📊 Architecture

```
User Question
     ↓
AIAssistantController (API)
     ↓
AIAssistantService (Orchestration)
     ↓
     ├─→ RAGService (Contexte BD)
     │   ├─→ Cours
     │   ├─→ Événements
     │   ├─→ Utilisateurs
     │   └─→ Activités
     └─→ OllamaService (IA)
         └─→ Llama 3.2 (Génération)
```

## 🔧 Configuration

### Variables d'Environnement (.env)
```env
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=llama3.2:3b
```

### Routes API
- `POST /ai-assistant/ask` - Poser une question
- `GET /ai-assistant/suggestions` - Obtenir suggestions
- `GET /ai-assistant/status` - Vérifier statut (admin)
- `GET /ai-assistant/test` - Page de test (dev)

## 💡 Exemples d'Utilisation

### Étudiant
```javascript
// Recommandation de cours
fetch('/ai-assistant/ask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        question: "Quels cours pour débuter en Python?"
    })
})
```

### Administrateur
```javascript
// Statistiques utilisateurs
fetch('/ai-assistant/ask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        question: "Combien d'utilisateurs inactifs depuis 7 jours?"
    })
})
```

## 🎨 Personnalisation

### Changer les Couleurs
Éditer `templates/ai_assistant/chat_widget.html.twig`:
```css
/* Ligne ~50 */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Ajouter des Intentions RAG
Éditer `src/Service/RAGService.php`:
```php
private function detectIntent(string $query): string
{
    if (preg_match('/(votre|pattern)/i', $query)) {
        return 'your_intent';
    }
    // ...
}
```

### Modifier le Prompt
Éditer `src/Service/OllamaService.php` → `buildSystemPrompt()`

## 🔐 Sécurité

### Implémenté
- ✅ Authentification requise
- ✅ Validation des entrées (max 500 chars)
- ✅ Filtrage données sensibles
- ✅ Timeouts configurés (30s)
- ✅ Logs des conversations

### Recommandé
- ⏳ Rate limiting (10 req/min)
- ⏳ CSRF protection
- ⏳ Content Security Policy

## 📈 Performance

### Temps de Réponse
- Contexte RAG: < 100ms
- Génération IA: 1-2 secondes
- Total moyen: 1-3 secondes

### Ressources
- RAM: 2-4GB (selon modèle)
- CPU: Modéré
- Stockage: ~2GB (modèle)

## 🐛 Dépannage

### "Ollama not available"
```bash
# Vérifier qu'Ollama est démarré
# Windows: Gestionnaire des tâches → Ollama
```

### Réponses lentes
```bash
# Utiliser modèle plus léger
ollama pull llama3.2:1b
# Modifier .env: OLLAMA_MODEL=llama3.2:1b
```

### Erreur de mémoire
```bash
# Modèles par taille:
# llama3.2:1b → 1GB RAM
# llama3.2:3b → 2GB RAM (recommandé)
# mistral:7b → 4GB RAM
```

## 📚 Documentation Complète

| Fichier | Description |
|---------|-------------|
| `DEMARRAGE_RAPIDE_IA.md` | Installation en 5 minutes |
| `GUIDE_INSTALLATION_IA.md` | Guide complet + dépannage |
| `ASSISTANT_IA_ARCHITECTURE.md` | Architecture technique |
| `ASSISTANT_IA_RESUME.md` | Résumé de toutes les fonctionnalités |
| `PROMPT_SYSTEM_IA.md` | Prompts système et exemples |

## 🚀 Prochaines Étapes

### Phase 1: Déploiement (Maintenant)
- [x] Installation Ollama
- [x] Configuration services
- [x] Intégration widget
- [ ] Tests utilisateurs
- [ ] Ajustements prompts

### Phase 2: Optimisation (Semaine 1-2)
- [ ] Cache des réponses
- [ ] Rate limiting
- [ ] Monitoring avancé

### Phase 3: Fonctionnalités Avancées (Mois 1-2)
- [ ] Fine-tuning modèle
- [ ] Support vocal
- [ ] Notifications proactives

## 🆘 Support

### Ressources
- Documentation Ollama: https://ollama.com/docs
- Modèles disponibles: https://ollama.com/library
- Symfony HttpClient: https://symfony.com/doc/current/http_client.html

### Problèmes Courants
Consultez `GUIDE_INSTALLATION_IA.md` section "Dépannage"

## 📝 Notes Importantes

### Modèles Recommandés
1. **llama3.2:3b** (Recommandé)
   - Taille: ~2GB
   - Vitesse: Rapide
   - Qualité: Excellente
   - Multilingue: ✅

2. **mistral:7b** (Alternative)
   - Taille: ~4GB
   - Vitesse: Moyenne
   - Qualité: Supérieure
   - Français: Excellent

### Limitations
- Nécessite Ollama installé localement
- Temps de réponse: 1-3 secondes
- Contexte limité à 500 tokens
- Pas de mémoire entre sessions

### Avantages
- ✅ 100% gratuit et open source
- ✅ Fonctionne en local (pas de coûts API)
- ✅ Données privées (pas d'envoi externe)
- ✅ Personnalisable à 100%
- ✅ Multilingue natif

## 🎉 Conclusion

Vous avez maintenant un assistant IA complet qui:
- Comprend le langage naturel
- Accède à votre base de données
- Recommande des cours personnalisés
- Propose des événements pertinents
- Aide à la gestion des utilisateurs
- Fonctionne 100% en local et gratuit!

**Prêt à démarrer?** Suivez `DEMARRAGE_RAPIDE_IA.md`!

---

**Créé avec ❤️ pour AutoLearn**
**Version**: 1.0.0
**Date**: Février 2026
