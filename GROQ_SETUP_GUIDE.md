# 🚀 Guide de Configuration Groq - Assistant IA

## ✅ Ce qui est déjà fait

Ton assistant IA est prêt avec:
- ✅ GroqService créé
- ✅ LanguageDetectorService créé  
- ✅ AIAssistantService mis à jour
- ✅ Configuration services_groq.yaml
- ✅ Fichiers .env et .env.example mis à jour

## 🔑 Étape 1: Obtenir ta clé API Groq (5 min)

1. Va sur **https://console.groq.com**
2. Clique sur **"Sign Up"** (ou "Log In" si tu as déjà un compte)
3. Crée un compte gratuit avec ton email
4. Une fois connecté, va dans **"API Keys"** dans le menu
5. Clique sur **"Create API Key"**
6. Donne un nom à ta clé (ex: "AutoLearn Dev")
7. **Copie la clé** (elle commence par `gsk_...`)

⚠️ **Important**: Sauvegarde cette clé, tu ne pourras plus la voir après!

---

## 🔧 Étape 2: Configurer ton .env (2 min)

Ouvre ton fichier `autolearn/.env` et remplace:

```env
GROQ_API_KEY=your_groq_api_key_here
```

Par ta vraie clé:

```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Les autres paramètres sont déjà configurés:
```env
GROQ_API_URL=https://api.groq.com/openai/v1/chat/completions
GROQ_MODEL=llama3-70b-8192
```

---

## 🧪 Étape 3: Tester l'Assistant (5 min)

### Test 1: Vérifier que Groq fonctionne

Ouvre ton terminal et lance:

```bash
cd autolearn
php bin/console cache:clear
```

### Test 2: Tester dans l'interface

1. Lance ton serveur Symfony:
```bash
symfony server:start
```

2. Ouvre ton navigateur sur `http://localhost:8000`

3. Connecte-toi (étudiant ou admin)

4. Cherche le widget chat en bas à droite 💬

5. Teste ces questions:

**En français**:
- "Quels cours me recommandes-tu?"
- "Montre-moi les événements à venir"
- "Je veux apprendre Python"

**En anglais**:
- "What courses do you recommend?"
- "Show me upcoming events"
- "I want to learn JavaScript"

**Autre langue (pour tester le refus)**:
- "أريد تعلم البرمجة" (Arabe)
- "我想学编程" (Chinois)

Tu devrais voir un message bilingue disant que seuls FR et EN sont supportés.

---

## 📊 Étape 4: Vérifier les logs (Optionnel)

Pour voir ce qui se passe en coulisses:

```bash
tail -f var/log/dev.log | grep -E "(groq|ai_assistant)"
```

Tu verras:
- Les requêtes envoyées à Groq
- Les réponses reçues
- Le nombre de tokens utilisés
- Les erreurs éventuelles

---

## 🎯 Fonctionnalités Disponibles

### Pour les Étudiants 👨‍🎓

L'assistant peut:
- ✅ Recommander des cours selon niveau/intérêts
- ✅ Proposer des exercices adaptés
- ✅ Suggérer des challenges
- ✅ Recommander des events
- ✅ Proposer des communautés
- ✅ Répondre aux questions sur les cours
- ✅ Expliquer des concepts

### Pour les Admins 👨‍💼

L'assistant peut:
- ✅ Créer/modifier des étudiants
- ✅ Filtrer/rechercher des étudiants
- ✅ Créer des cours, chapitres, ressources
- ✅ Créer des exercices et challenges
- ✅ Créer des events
- ✅ Obtenir des statistiques
- ✅ Gérer les communautés

---

## 🌍 Support Multilingue

### Langues Supportées
- ✅ **Français** (FR)
- ✅ **Anglais** (EN)

### Détection Automatique
L'assistant détecte automatiquement la langue et répond dans la même langue.

### Autres Langues
Si l'utilisateur parle arabe, chinois, espagnol, etc., l'assistant répond poliment en FR + EN qu'il ne comprend que ces deux langues.

---

## ⚡ Performances

Avec Groq, tu auras:
- **Vitesse**: Réponses en 0.3-1 seconde (vs 2-5 sec avec Ollama)
- **Qualité**: Modèle 70B paramètres (vs 1B avec Ollama)
- **Compréhension**: Meilleure compréhension du langage naturel
- **Pas d'installation**: Tout est dans le cloud

---

## 🔒 Limites Gratuites Groq

Le plan gratuit Groq offre:
- **Requêtes**: 30 requêtes/minute
- **Tokens**: 6,000 tokens/minute
- **Requêtes/jour**: ~14,400 requêtes

C'est largement suffisant pour le développement et même pour une petite production!

---

## 🐛 Dépannage

### Problème: "Groq not available"

**Solution 1**: Vérifie ta clé API
```bash
# Dans autolearn/.env
GROQ_API_KEY=gsk_... # Doit commencer par gsk_
```

**Solution 2**: Vérifie ta connexion internet
```bash
curl https://api.groq.com/openai/v1/models
```

**Solution 3**: Vide le cache
```bash
php bin/console cache:clear
```

### Problème: "Rate limit exceeded"

Tu as dépassé la limite de 30 requêtes/minute. Attends 1 minute et réessaye.

### Problème: L'assistant ne répond pas

1. Vérifie les logs:
```bash
tail -f var/log/dev.log
```

2. Vérifie que le service est bien configuré:
```bash
php bin/console debug:container GroqService
```

---

## 📚 Documentation Complète

Pour plus de détails, consulte:
- `ASSISTANT_IA_GROQ_VISION.md` - Vision complète du projet
- `ASSISTANT_IA_ARCHITECTURE.md` - Architecture technique
- `README_ASSISTANT_IA.md` - Guide utilisateur

---

## 🎉 C'est Prêt!

Une fois ta clé API configurée, ton assistant IA est opérationnel!

**Avantages vs Ollama**:
- ⚡ 5x plus rapide
- 🧠 70x plus de paramètres (70B vs 1B)
- 🌍 Support multilingue natif
- 🚀 Pas d'installation locale
- 💰 Gratuit (avec limites raisonnables)

**Prochaines Étapes**:
1. Configure ta clé API Groq
2. Teste l'assistant
3. Personnalise les prompts si besoin
4. Déploie en production

---

**Créé par**: Ilef Yousfi  
**Date**: Février 2026  
**Statut**: ✅ Prêt à utiliser
