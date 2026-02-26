# 🤖 Guide d'Installation - Assistant IA avec RAG

## 📋 Prérequis

- PHP 8.1+
- Symfony 6.4
- MySQL/MariaDB
- 4GB RAM minimum
- Windows 10/11 ou Linux

## 🚀 Installation Rapide

### Étape 1: Installer Ollama

#### Windows
```bash
# Télécharger et installer Ollama
winget install Ollama.Ollama

# OU télécharger depuis: https://ollama.com/download/windows
```

#### Linux
```bash
curl -fsSL https://ollama.com/install.sh | sh
```

### Étape 2: Télécharger le Modèle IA

```bash
# Modèle recommandé: Llama 3.2 (3B) - Léger et rapide
ollama pull llama3.2:3b

# Alternative: Mistral 7B - Plus puissant mais plus lourd
# ollama pull mistral:7b
```

### Étape 3: Vérifier l'Installation

```bash
# Vérifier qu'Ollama fonctionne
ollama list

# Tester le modèle
ollama run llama3.2:3b "Bonjour, comment vas-tu?"
```

### Étape 4: Configurer Symfony

#### A. Ajouter les variables d'environnement

Éditer `.env`:
```env
###> AI Assistant ###
OLLAMA_URL=http://localhost:11434
OLLAMA_MODEL=llama3.2:3b
###< AI Assistant ###
```

#### B. Configurer les services

Éditer `config/services.yaml`:
```yaml
parameters:
    ollama_url: '%env(OLLAMA_URL)%'
    ollama_model: '%env(OLLAMA_MODEL)%'

services:
    # ... existing services ...
    
    # Ollama Service
    App\Service\OllamaService:
        arguments:
            $ollamaUrl: '%ollama_url%'
            $model: '%ollama_model%'
    
    # RAG Service
    App\Service\RAGService:
        arguments:
            $em: '@doctrine.orm.entity_manager'
            $security: '@security.helper'
            $coursRepository: '@App\Repository\CoursRepository'
            $evenementRepository: '@App\Repository\EvenementRepository'
            $userRepository: '@App\Repository\UserRepository'
            $activityRepository: '@App\Bundle\UserActivityBundle\Repository\UserActivityRepository'
    
    # AI Assistant Service
    App\Service\AIAssistantService:
        arguments:
            $ollamaService: '@App\Service\OllamaService'
            $ragService: '@App\Service\RAGService'
            $logger: '@logger'
```

### Étape 5: Inclure le Widget Chat

Éditer `templates/base.html.twig` (ou votre template principal):
```twig
<!DOCTYPE html>
<html>
<head>
    {# ... existing head content ... #}
</head>
<body>
    {# ... existing body content ... #}
    
    {# AI Chat Widget - Avant la fermeture du body #}
    {% include 'ai_assistant/chat_widget.html.twig' %}
</body>
</html>
```

### Étape 6: Tester l'Installation

#### A. Vérifier le statut (Admin uniquement)
```
http://localhost:8000/ai-assistant/status
```

#### B. Page de test (Dev uniquement)
```
http://localhost:8000/ai-assistant/test
```

#### C. Utiliser le widget
1. Connectez-vous à la plateforme
2. Cliquez sur le bouton flottant 🤖 en bas à droite
3. Posez une question!

## 🎯 Exemples de Questions

### Pour les Étudiants
```
💬 "Quels cours pour débuter en Python?"
💬 "Événements cette semaine?"
💬 "Mon historique d'activités?"
💬 "Recommande-moi un cours"
💬 "Comment progresser rapidement?"
```

### Pour les Administrateurs
```
💬 "Combien d'utilisateurs actifs?"
💬 "Utilisateurs inactifs depuis 7 jours?"
💬 "Statistiques de la plateforme?"
💬 "Cours les plus populaires?"
```

## 🔧 Configuration Avancée

### Changer de Modèle

```bash
# Télécharger un autre modèle
ollama pull mistral:7b

# Mettre à jour .env
OLLAMA_MODEL=mistral:7b
```

### Ajuster les Paramètres

Dans votre requête API, vous pouvez passer des options:
```javascript
fetch('/ai-assistant/ask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        question: "Ma question",
        temperature: 0.8,  // Créativité (0.0 - 1.0)
        model: "mistral:7b" // Modèle spécifique
    })
})
```

### Paramètres de Température
- `0.0 - 0.3`: Réponses très précises et déterministes
- `0.4 - 0.7`: Équilibre (recommandé)
- `0.8 - 1.0`: Réponses créatives et variées

## 🐛 Dépannage

### Problème: "Ollama not available"

**Solution 1**: Vérifier qu'Ollama est démarré
```bash
# Windows: Vérifier dans le gestionnaire des tâches
# Ou redémarrer Ollama

# Linux: Vérifier le service
systemctl status ollama
```

**Solution 2**: Vérifier l'URL
```bash
# Tester manuellement
curl http://localhost:11434/api/tags
```

### Problème: Réponses lentes

**Solution 1**: Utiliser un modèle plus léger
```bash
ollama pull llama3.2:3b  # Plus rapide que mistral:7b
```

**Solution 2**: Augmenter la RAM allouée
```bash
# Éditer la configuration Ollama
# Windows: Variables d'environnement système
OLLAMA_MAX_LOADED_MODELS=1
OLLAMA_NUM_PARALLEL=1
```

### Problème: Erreur de mémoire

**Solution**: Utiliser un modèle plus petit
```bash
# Modèles par taille:
# - llama3.2:1b (1GB RAM)
# - llama3.2:3b (2GB RAM) ← Recommandé
# - mistral:7b (4GB RAM)
# - llama3:8b (8GB RAM)

ollama pull llama3.2:1b
```

### Problème: Le widget ne s'affiche pas

**Vérifications**:
1. L'utilisateur est-il connecté? (Le widget n'apparaît que pour les utilisateurs authentifiés)
2. Le template est-il inclus dans base.html.twig?
3. Vérifier la console JavaScript pour les erreurs

## 📊 Monitoring

### Logs Ollama
```bash
# Windows
%LOCALAPPDATA%\Ollama\logs\

# Linux
journalctl -u ollama -f
```

### Logs Symfony
```bash
# Logs de l'assistant IA
tail -f var/log/dev.log | grep "AI Assistant"
```

### Métriques de Performance
```bash
# Temps de réponse moyen
# Vérifier dans les logs ou via /ai-assistant/status
```

## 🔐 Sécurité

### Rate Limiting (Recommandé)

Installer le composant Symfony Rate Limiter:
```bash
composer require symfony/rate-limiter
```

Configurer dans `config/packages/rate_limiter.yaml`:
```yaml
framework:
    rate_limiter:
        ai_assistant:
            policy: 'sliding_window'
            limit: 10
            interval: '1 minute'
```

### Filtrage des Données Sensibles

Le RAGService filtre automatiquement:
- ✅ Mots de passe (jamais inclus)
- ✅ Tokens d'authentification
- ✅ Données personnelles sensibles

## 🚀 Optimisations

### 1. Cache des Réponses Fréquentes

```php
// À implémenter dans AIAssistantService
private function getCachedResponse(string $question): ?string
{
    // Utiliser Symfony Cache
    return $this->cache->get('ai_response_' . md5($question));
}
```

### 2. Pré-chargement du Modèle

```bash
# Garder le modèle en mémoire
ollama run llama3.2:3b
# Laisser tourner en arrière-plan
```

### 3. Compression des Contextes

```php
// Limiter la taille du contexte RAG
private function compressContext(array $data): array
{
    // Garder seulement les 10 premiers résultats
    return array_slice($data, 0, 10);
}
```

## 📈 Prochaines Étapes

1. ✅ Installation de base
2. ✅ Configuration des services
3. ✅ Intégration du widget
4. ⏳ Fine-tuning du modèle (optionnel)
5. ⏳ Support vocal (futur)
6. ⏳ Notifications proactives (futur)

## 🆘 Support

### Ressources
- Documentation Ollama: https://ollama.com/docs
- Modèles disponibles: https://ollama.com/library
- Symfony HttpClient: https://symfony.com/doc/current/http_client.html

### Problèmes Courants
- GitHub Issues: [Votre repo]
- Discord: [Votre serveur]
- Email: support@autolearn.com

---

**Installation terminée!** 🎉

Votre assistant IA est maintenant opérationnel. Testez-le en posant des questions!
