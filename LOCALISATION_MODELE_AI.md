# 📍 LOCALISATION EXACTE DU MODÈLE AI ET DES PROMPTS

## 🎯 VUE D'ENSEMBLE

```
┌─────────────────────────────────────────────────────────────┐
│  FICHIER: src/Service/AIReportService.php                   │
│  RÔLE: Service principal pour l'intégration du modèle AI    │
└─────────────────────────────────────────────────────────────┘
```

---

## 📂 STRUCTURE DU FICHIER AIReportService.php

### 1️⃣ CONFIGURATION DU MODÈLE (Lignes 8-24)

```php
class AIReportService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;              // ← Clé API Hugging Face
    private string $model;               // ← Nom du modèle (Mistral-7B)
    private FeedbackAnalyticsService $analyticsService;

    public function __construct(
        HttpClientInterface $httpClient,
        FeedbackAnalyticsService $analyticsService,
        string $huggingfaceApiKey,      // ← Injecté depuis .env.local
        string $huggingfaceModel        // ← Injecté depuis .env.local
    ) {
        $this->httpClient = $httpClient;
        $this->analyticsService = $analyticsService;
        $this->apiKey = $huggingfaceApiKey;
        $this->model = $huggingfaceModel;  // ← "mistralai/Mistral-7B-Instruct-v0.2"
    }
}
```

**📍 Localisation:** Lignes 8-24
**🔑 Variables importantes:**
- `$this->apiKey` → Ton token Hugging Face
- `$this->model` → Le modèle utilisé

---

### 2️⃣ PROMPTS (Instructions pour l'AI)

#### 🔹 PROMPT 1: Rapport d'Analyse (Lignes 63-85)

```php
private function buildAnalysisPrompt(array $data): string
{
    $byTypeText = $this->formatByTypeData($data['by_type']);
    $commentsText = $this->formatComments($data['recent_comments']);

    return <<<PROMPT
[INST] Tu es un expert en analyse de données éducatives. 
Analyse les données suivantes et génère un rapport professionnel en français.

DONNÉES DES ÉVÉNEMENTS:
{$byTypeText}                          // ← Données injectées ici

COMMENTAIRES RÉCENTS DES ÉTUDIANTS:
{$commentsText}                        // ← Commentaires injectés ici

Génère un rapport d'analyse détaillé incluant:
1. Performance globale des événements
2. Types d'événements les plus appréciés (classement)
3. Analyse par catégorie (organisation, contenu, lieu, animation)
4. Tendances détectées dans les commentaires
5. Taux de satisfaction général

Format: Rapport professionnel avec sections claires, émojis pour la lisibilité, 
et données chiffrées. [/INST]
PROMPT;
}
```

**📍 Localisation:** Lignes 63-85
**🎯 Rôle:** Construit le prompt pour le rapport d'analyse
**📝 Format:** Utilise la syntaxe `[INST]...[/INST]` spécifique à Mistral

---

#### 🔹 PROMPT 2: Recommandations d'Événements (Lignes 90-112)

```php
private function buildRecommendationPrompt(array $data): string
{
    $byTypeText = $this->formatByTypeData($data['by_type']);
    $commentsText = $this->formatComments($data['recent_comments']);

    return <<<PROMPT
[INST] Tu es un expert en planification d'événements éducatifs. 
Basé sur ces données, recommande 3 événements à organiser.

DONNÉES DES ÉVÉNEMENTS PASSÉS:
{$byTypeText}

COMMENTAIRES DES ÉTUDIANTS:
{$commentsText}

Pour chaque événement recommandé, fournis:
1. Titre et type d'événement
2. Durée suggérée
3. Capacité recommandée (nombre d'équipes)
4. Justification détaillée (pourquoi cet événement?)
5. Satisfaction prédite (sur 5)
6. Sujets/thèmes suggérés basés sur les commentaires

Format: Liste numérotée, professionnelle, avec émojis et données chiffrées. [/INST]
PROMPT;
}
```

**📍 Localisation:** Lignes 90-112
**🎯 Rôle:** Construit le prompt pour les recommandations

---

#### 🔹 PROMPT 3: Suggestions d'Amélioration (Lignes 117-145)

```php
private function buildImprovementPrompt(array $data): string
{
    $byTypeText = $this->formatByTypeData($data['by_type']);
    $commentsText = $this->formatComments($data['recent_comments']);

    return <<<PROMPT
[INST] Tu es un consultant en amélioration continue pour des événements éducatifs. 
Analyse ces données et propose un plan d'amélioration.

DONNÉES DES ÉVÉNEMENTS:
{$byTypeText}

COMMENTAIRES DES ÉTUDIANTS:
{$commentsText}

Génère un plan d'amélioration incluant:
1. Problèmes identifiés (classés par priorité: HAUTE, MOYENNE, BASSE)
2. Pour chaque problème:
   - Description du problème
   - Preuves (citations de commentaires si pertinent)
   - Actions recommandées (concrètes et applicables)
   - Impact estimé sur la satisfaction
3. Quick wins (améliorations rapides à implémenter)
4. Améliorations à long terme

Format: Plan structuré, professionnel, avec émojis et priorités claires. [/INST]
PROMPT;
}
```

**📍 Localisation:** Lignes 117-145
**🎯 Rôle:** Construit le prompt pour les suggestions d'amélioration

---

### 3️⃣ APPEL API MISTRAL (Lignes 150-195)

```php
private function callMistralAPI(string $prompt): ?string
{
    try {
        $response = $this->httpClient->request('POST', 
            "https://api-inference.huggingface.co/models/{$this->model}",  // ← URL du modèle
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,  // ← Token d'authentification
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $prompt,                    // ← Le prompt envoyé au modèle
                    'parameters' => [
                        'max_new_tokens' => 1500,          // ← Longueur max de la réponse
                        'temperature' => 0.7,              // ← Créativité (0-1)
                        'top_p' => 0.9,                    // ← Diversité des réponses
                        'do_sample' => true,               // ← Échantillonnage activé
                        'return_full_text' => false,       // ← Retourner seulement la réponse
                    ],
                ],
                'timeout' => 60,                           // ← Timeout de 60 secondes
            ]
        );

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();
        
        // Extraire le texte généré
        $generatedText = $data[0]['generated_text'] ?? null;
        
        if (!$generatedText) {
            return null;
        }
        
        // Nettoyer le texte (enlever le prompt si présent)
        $generatedText = str_replace($prompt, '', $generatedText);
        $generatedText = trim($generatedText);
        
        return $generatedText;  // ← Retourne le rapport généré
        
    } catch (\Exception $e) {
        error_log('Erreur API Mistral: ' . $e->getMessage());
        return null;
    }
}
```

**📍 Localisation:** Lignes 150-195
**🎯 Rôle:** Envoie le prompt au modèle et récupère la réponse
**🔗 URL API:** `https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2`

---

## 🔄 FLUX COMPLET D'EXÉCUTION

```
1. ADMIN CLIQUE SUR BOUTON
   ↓
   templates/backoffice/evenement/index.html.twig (ligne 50-60)
   Fonction JavaScript: generateReport('analysis')
   
2. APPEL AJAX
   ↓
   POST /backoffice/evenement/ai/generate-analysis
   
3. CONTRÔLEUR
   ↓
   src/Controller/EvenementController.php (ligne 120-140)
   Méthode: generateAIAnalysis()
   
4. SERVICE AI
   ↓
   src/Service/AIReportService.php
   
   4.1 Préparer les données (ligne 29)
       $data = $this->analyticsService->prepareDataForAI();
   
   4.2 Construire le prompt (ligne 31)
       $prompt = $this->buildAnalysisPrompt($data);
   
   4.3 Appeler l'API Mistral (ligne 33)
       return $this->callMistralAPI($prompt);
   
5. API HUGGING FACE
   ↓
   URL: https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2
   Méthode: POST
   Headers: Authorization: Bearer {token}
   Body: { "inputs": "{prompt}", "parameters": {...} }
   
6. MODÈLE MISTRAL-7B
   ↓
   Traite le prompt
   Génère le rapport
   Retourne le texte
   
7. RÉPONSE
   ↓
   Le rapport est affiché dans le navigateur
```

---

## 📊 PARAMÈTRES DU MODÈLE

### Configuration dans l'appel API (Ligne 158-165)

```php
'parameters' => [
    'max_new_tokens' => 1500,    // Longueur maximale de la réponse (en tokens)
    'temperature' => 0.7,        // Créativité: 0=déterministe, 1=créatif
    'top_p' => 0.9,              // Diversité: garde les 90% meilleurs tokens
    'do_sample' => true,         // Active l'échantillonnage (variété)
    'return_full_text' => false, // Retourne seulement la réponse (pas le prompt)
]
```

**🎛️ Tu peux modifier ces paramètres:**
- `max_new_tokens`: Augmente si tu veux des rapports plus longs
- `temperature`: Baisse (0.5) pour plus de précision, monte (0.9) pour plus de créativité
- `top_p`: Garde à 0.9 (valeur optimale)

---

## 🔑 CONFIGURATION EXTERNE

### Fichier .env.local (Configuration)

```env
HUGGINGFACE_API_KEY=hf_ton_token_ici
HUGGINGFACE_MODEL=mistralai/Mistral-7B-Instruct-v0.2
```

**📍 Localisation:** `.env.local` (racine du projet)
**🔗 Injection:** Via `config/services.yaml` (lignes 30-35)

---

## 📝 RÉSUMÉ VISUEL

```
┌──────────────────────────────────────────────────────────────┐
│  FICHIER PRINCIPAL: src/Service/AIReportService.php         │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  📍 LIGNE 8-24:   Configuration (API Key + Modèle)          │
│  📍 LIGNE 63-85:  Prompt Rapport d'Analyse                  │
│  📍 LIGNE 90-112: Prompt Recommandations                    │
│  📍 LIGNE 117-145: Prompt Améliorations                     │
│  📍 LIGNE 150-195: Appel API Mistral                        │
│                                                              │
│  🔗 URL API:                                                 │
│  https://api-inference.huggingface.co/models/               │
│  mistralai/Mistral-7B-Instruct-v0.2                         │
│                                                              │
│  🔑 Token: Depuis .env.local                                 │
│  🎯 Modèle: mistralai/Mistral-7B-Instruct-v0.2              │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## ✅ POINTS CLÉS

1. **Le modèle est spécifié:** Ligne 17 (`$this->model`)
2. **Les prompts sont dans:** Lignes 63-145 (3 méthodes)
3. **L'appel API est dans:** Lignes 150-195 (méthode `callMistralAPI`)
4. **Le token vient de:** `.env.local` (variable `HUGGINGFACE_API_KEY`)
5. **L'URL complète est:** `https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2`

---

**Tout est dans UN SEUL fichier: `src/Service/AIReportService.php`** 🎯
