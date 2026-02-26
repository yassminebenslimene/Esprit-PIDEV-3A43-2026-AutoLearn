# Architecture Assistant IA avec RAG - AutoLearn

## 🎯 Objectif
Créer un assistant intelligent qui:
- Accède à la base de données pour collecter les informations
- Recommande des cours selon les besoins de l'utilisateur
- Propose des événements pertinents
- Aide à la gestion des utilisateurs
- Utilise RAG (Retrieval-Augmented Generation) pour des réponses contextuelles

## 🏗️ Architecture Technique

### 1. Modèle IA Open Source
**Choix: Ollama avec Llama 3.2 (3B)**
- ✅ Gratuit et open source
- ✅ Fonctionne en local (pas de coûts API)
- ✅ Léger (3B paramètres)
- ✅ Multilingue (FR, EN, AR, ES)
- ✅ Rapide pour les recommandations

**Alternative: Mistral 7B**
- Plus puissant mais plus lourd
- Excellent en français

### 2. Stack Technique
```
┌─────────────────────────────────────────┐
│         Interface Utilisateur           │
│    (Chat Widget dans la plateforme)     │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│      AIAssistantController.php          │
│   (Gère les requêtes utilisateur)       │
└──────────────┬──────────────────────────┘
               │
┌──────────────▼──────────────────────────┐
│      AIAssistantService.php             │
│  - Traite les questions                 │
│  - Appelle le RAG                       │
│  - Formate les réponses                 │
└──────────────┬──────────────────────────┘
               │
       ┌───────┴────────┐
       │                │
┌──────▼─────┐  ┌──────▼──────────────┐
│ RAGService │  │  OllamaService      │
│            │  │  (Modèle IA)        │
│ - Collecte │  │  - Génère réponses  │
│   contexte │  │  - Comprend intent  │
│ - Recherche│  │                     │
│   BD       │  │                     │
└──────┬─────┘  └─────────────────────┘
       │
┌──────▼──────────────────────────────────┐
│        Base de Données MySQL            │
│  - Users (activités, niveau, cours)     │
│  - Cours (matière, niveau, chapitres)   │
│  - Événements (date, lieu, météo)       │
│  - UserActivity (historique)            │
└─────────────────────────────────────────┘
```

### 3. Fonctionnalités RAG

#### A. Collecte de Contexte (Retrieval)
```php
// Exemples de requêtes contextuelles
- "Quels cours pour un débutant en Python?"
  → Recherche: cours WHERE matiere='Python' AND niveau='DEBUTANT'
  
- "Événements cette semaine?"
  → Recherche: evenements WHERE date BETWEEN now() AND +7 days
  
- "Mon historique d'activités?"
  → Recherche: user_activity WHERE user_id = current_user
```

#### B. Génération de Réponses (Generation)
Le modèle IA utilise le contexte collecté pour générer des réponses naturelles et personnalisées.

### 4. Cas d'Usage Prioritaires

#### 🎓 Recommandation de Cours
```
User: "Je veux apprendre la programmation web"
IA: 
1. Analyse le niveau de l'utilisateur (débutant/intermédiaire/avancé)
2. Recherche les cours disponibles en développement web
3. Recommande un parcours personnalisé
4. Propose les chapitres dans l'ordre optimal
```

#### 📅 Gestion d'Événements
```
User: "Quels événements cette semaine?"
IA:
1. Récupère les événements à venir
2. Affiche la météo prévue
3. Indique les places disponibles
4. Propose l'inscription directe
```

#### 👥 Gestion Utilisateurs (Admin)
```
Admin: "Combien d'utilisateurs inactifs depuis 7 jours?"
IA:
1. Compte les users avec lastLoginAt > 7 jours
2. Liste les utilisateurs concernés
3. Propose des actions (suspension, email de rappel)
```

#### 📊 Statistiques et Insights
```
User: "Mes progrès ce mois-ci?"
IA:
1. Analyse l'historique d'activités
2. Calcule les cours complétés
3. Affiche les quiz réussis
4. Suggère les prochaines étapes
```

## 🚀 Implémentation

### Phase 1: Infrastructure (Aujourd'hui)
- [x] Installation Ollama
- [x] Service OllamaService.php
- [x] Service RAGService.php
- [x] Service AIAssistantService.php
- [x] Controller AIAssistantController.php

### Phase 2: Interface Chat (Aujourd'hui)
- [x] Widget chat flottant
- [x] Interface conversationnelle
- [x] Historique des messages
- [x] Typing indicators

### Phase 3: Fonctionnalités RAG (Aujourd'hui)
- [x] Recommandation de cours
- [x] Recherche d'événements
- [x] Statistiques utilisateur
- [x] Gestion admin

### Phase 4: Optimisation (Futur)
- [ ] Cache des réponses fréquentes
- [ ] Fine-tuning du modèle
- [ ] Support vocal
- [ ] Notifications proactives

## 📝 Prompt System

### Prompt Principal (System)
```
Tu es un assistant intelligent pour AutoLearn, une plateforme d'apprentissage en ligne.

CONTEXTE:
- Plateforme: AutoLearn (cours, événements, challenges, communauté)
- Utilisateur actuel: {user_name} ({user_role}, niveau: {user_level})
- Langue: {locale}

CAPACITÉS:
1. Recommander des cours adaptés au niveau de l'utilisateur
2. Proposer des événements pertinents
3. Fournir des statistiques sur les progrès
4. Aider à la navigation sur la plateforme
5. Répondre aux questions sur les cours disponibles

DONNÉES DISPONIBLES:
{context_data}

INSTRUCTIONS:
- Réponds de manière concise et claire
- Utilise les données fournies dans le contexte
- Propose des actions concrètes (liens, boutons)
- Sois encourageant et positif
- Adapte ton langage au niveau de l'utilisateur
```

## 🔧 Configuration Requise

### Serveur
- PHP 8.1+
- MySQL/MariaDB
- 4GB RAM minimum (pour Ollama)
- Ollama installé localement

### Installation Ollama
```bash
# Windows
winget install Ollama.Ollama

# Télécharger le modèle
ollama pull llama3.2:3b
```

## 🎨 Interface Utilisateur

### Widget Chat
- Position: Coin inférieur droit
- Icône: 💬 ou 🤖
- Couleurs: Thème de la plateforme
- Responsive: Mobile et desktop
- Animations: Smooth et modernes

### Exemples de Questions
```
💬 "Quels cours pour débuter en Python?"
💬 "Événements cette semaine?"
💬 "Mon historique d'activités?"
💬 "Recommande-moi un cours"
💬 "Combien d'utilisateurs actifs?" (admin)
```

## 🔐 Sécurité

- ✅ Authentification requise
- ✅ Filtrage des données sensibles
- ✅ Rate limiting (max 10 requêtes/minute)
- ✅ Validation des entrées
- ✅ Logs des conversations (RGPD compliant)

## 📊 Métriques de Succès

- Taux d'utilisation de l'assistant
- Satisfaction utilisateur (feedback)
- Temps de réponse moyen
- Précision des recommandations
- Réduction du temps de navigation

---

**Prochaine étape**: Implémentation du code PHP et JavaScript
