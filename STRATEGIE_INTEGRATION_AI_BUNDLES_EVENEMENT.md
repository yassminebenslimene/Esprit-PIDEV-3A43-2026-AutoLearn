# 🚀 STRATÉGIE D'INTÉGRATION - AI & BUNDLES MODULE EVENEMENT

## 📋 TABLE DES MATIÈRES
1. [Vue d'ensemble du projet](#vue-densemble)
2. [Architecture et flux de données](#architecture)
3. [Fonctionnalités AI](#fonctionnalités-ai)
4. [Bundles Symfony](#bundles-symfony)
5. [Système de Feedback et Rating](#système-feedback)
6. [Visualisations et Statistiques](#visualisations)
7. [Plan d'implémentation](#plan-implémentation)
8. [Configuration et déploiement](#configuration)

---

## 🎯 VUE D'ENSEMBLE DU PROJET {#vue-densemble}

### Objectifs principaux
1. **Génération automatique de descriptions** d'événements via AI
2. **Analyse de sentiment** des feedbacks participants
3. **Prédiction du nombre optimal** de participants
4. **Système de feedback et rating** après événements
5. **Calendrier interactif** pour visualiser les événements
6. **Workflow automatisé** pour la gestion des statuts
7. **Statistiques visuelles** avec Chart.js

### État actuel du module
- ✅ Entités: Evenement, Participation, Equipe, Etudiant
- ✅ Services: WeatherService, EmailService, BadgeService, CertificateService
- ✅ Statuts: Planifié, En cours, Annulé
- ✅ Validation automatique des participations
- ✅ Envoi automatique de certificats

### Valeur ajoutée globale
- 🎯 **Pour l'Admin**: Décisions basées sur les données, gain de temps
- 🎯 **Pour les Étudiants**: Meilleure expérience, événements plus pertinents
- 🎯 **Pour la Plateforme**: Augmentation du taux de participation, optimisation

---

## 🏗️ ARCHITECTURE ET FLUX DE DONNÉES {#architecture}

```
┌─────────────────────────────────────────────────────────────┐
│                    MODULE EVENEMENT                          │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐  │
│  │  FRONTOFFICE │    │  BACKOFFICE  │    │   SERVICES   │  │
│  │              │    │              │    │              │  │
│  │ • Liste      │    │ • CRUD       │    │ • Email      │  │
│  │ • Détails    │◄───┤ • Stats      │◄───┤ • Weather    │  │
│  │ • Participer │    │ • Calendar   │    │ • Badge      │  │
│  │ • Feedback   │    │ • AI Tools   │    │ • Certificate│  │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘  │
│         │                   │                    │          │
│         └───────────────────┴────────────────────┘          │
│                             │                                │
│                    ┌────────▼────────┐                      │
│                    │   NOUVELLES     │                      │
│                    │   ENTITÉS       │                      │
│                    ├─────────────────┤                      │
│                    │ • Feedback      │                      │
│                    │ • Rating        │                      │
│                    │ • AIAnalysis    │                      │
│                    │ • Prediction    │                      │
│                    └────────┬────────┘                      │
│                             │                                │
│         ┌───────────────────┼───────────────────┐          │
│         │                   │                   │          │
│    ┌────▼─────┐      ┌─────▼──────┐     ┌─────▼──────┐   │
│    │ AI APIs  │      │  Bundles   │     │  Chart.js  │   │
│    │          │      │            │     │            │   │
│    │ • Hugging│      │ • Calendar │     │ • Stats    │   │
│    │   Face   │      │ • Workflow │     │ • Graphs   │   │
│    │ • OpenAI │      │            │     │            │   │
│    └──────────┘      └────────────┘     └────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🤖 FONCTIONNALITÉS AI {#fonctionnalités-ai}

### 1. GÉNÉRATION AUTOMATIQUE DE DESCRIPTIONS

#### 🎯 Objectif
Aider l'admin à créer des descriptions attractives et professionnelles automatiquement.

#### 🔧 Choix du modèle (API en ligne - GRATUIT)

**Option 1: Hugging Face Inference API (RECOMMANDÉ) ⭐⭐⭐⭐⭐**
- **Modèle**: `mistralai/Mistral-7B-Instruct-v0.2`
- **Avantages**:
  - 100% gratuit (rate limit: 1000 requêtes/jour)
  - Pas d'installation locale
  - Multilingue (français + anglais)
  - Très performant pour la génération de texte
  - API simple à utiliser
- **Inconvénients**:
  - Nécessite une clé API (gratuite)
  - Limite de 1000 requêtes/jour (largement suffisant)

**Option 2: OpenAI GPT-3.5-turbo (Alternative payante mais performante)**
- **Avantages**:
  - Très performant
  - Réponses de haute qualité
  - API stable
- **Inconvénients**:
  - Payant ($0.002 par 1K tokens)
  - Nécessite carte bancaire

**Option 3: Cohere API (Gratuit avec limitations)**
- **Avantages**:
  - Gratuit jusqu'à 100 requêtes/minute
  - Bonne qualité
- **Inconvénients**:
  - Moins performant que Mistral

#### 📦 Implémentation recommandée: Hugging Face

**Étape 1: Créer un compte Hugging Face**
1. Aller sur https://huggingface.co/
2. Créer un compte gratuit
3. Aller dans Settings → Access Tokens
4. Créer un token avec permission "Read"

**Étape 2: Configuration dans .env.local**
```env
# Hugging Face API
HUGGINGFACE_API_KEY=hf_xxxxxxxxxxxxxxxxxxxxxxxxxx
HUGGINGFACE_MODEL=mistralai/Mistral-7B-Instruct-v0.2
```

**Étape 3: Créer le service AIDescriptionService**
```php
// src/Service/AIDescriptionService.php
<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AIDescriptionService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $model;

    public function __construct(
        HttpClientInterface $httpClient,
        string $huggingfaceApiKey,
        string $huggingfaceModel
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $huggingfaceApiKey;
        $this->model = $huggingfaceModel;
    }

    /**
     * Génère une description d'événement via AI
     */
    public function generateEventDescription(
        string $titre,
        string $type,
        \DateTimeInterface $dateDebut,
        string $lieu
    ): ?string {
        try {
            $prompt = $this->buildPrompt($titre, $type, $dateDebut, $lieu);
            
            $response = $this->httpClient->request('POST', 
                "https://api-inference.huggingface.co/models/{$this->model}", 
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'inputs' => $prompt,
                        'parameters' => [
                            'max_new_tokens' => 200,
                            'temperature' => 0.7,
                            'top_p' => 0.9,
                            'do_sample' => true,
                        ],
                    ],
                    'timeout' => 30,
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
            
            // Nettoyer le texte (enlever le prompt)
            $description = str_replace($prompt, '', $generatedText);
            $description = trim($description);
            
            // Limiter à 500 caractères
            if (strlen($description) > 500) {
                $description = substr($description, 0, 497) . '...';
            }
            
            return $description;
            
        } catch (\Exception $e) {
            // Log l'erreur
            return null;
        }
    }

    /**
     * Construit le prompt pour l'AI
     */
    private function buildPrompt(
        string $titre,
        string $type,
        \DateTimeInterface $dateDebut,
        string $lieu
    ): string {
        $date = $dateDebut->format('d/m/Y');
        
        return <<<PROMPT
[INST] Tu es un assistant qui génère des descriptions professionnelles pour des événements éducatifs.

Génère une description attractive et professionnelle (150-200 mots) pour cet événement:
- Titre: {$titre}
- Type: {$type}
- Date: {$date}
- Lieu: {$lieu}

La description doit:
- Être engageante et motivante
- Expliquer les bénéfices pour les participants
- Être en français
- Être professionnelle mais accessible

Description: [/INST]
PROMPT;
    }

    /**
     * Améliore une description existante
     */
    public function improveDescription(string $currentDescription): ?string
    {
        try {
            $prompt = <<<PROMPT
[INST] Améliore cette description d'événement pour la rendre plus attractive et professionnelle:

"{$currentDescription}"

Amélioration: [/INST]
PROMPT;

            $response = $this->httpClient->request('POST', 
                "https://api-inference.huggingface.co/models/{$this->model}", 
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'inputs' => $prompt,
                        'parameters' => [
                            'max_new_tokens' => 250,
                            'temperature' => 0.7,
                        ],
                    ],
                    'timeout' => 30,
                ]
            );

            $data = $response->toArray();
            $improved = $data[0]['generated_text'] ?? null;
            
            if ($improved) {
                $improved = str_replace($prompt, '', $improved);
                return trim($improved);
            }
            
            return null;
            
        } catch (\Exception $e) {
            return null;
        }
    }
}
```

**Étape 4: Intégration dans le formulaire**
- Ajouter un bouton "Générer avec AI" dans le formulaire de création d'événement
- Appel AJAX vers un nouveau endpoint `/backoffice/evenement/generate-description`
- Afficher la description générée dans le champ (l'admin peut la modifier)

**Valeur ajoutée:**
- ⏱️ Gain de temps: 5 minutes → 10 secondes
- 📝 Descriptions cohérentes et professionnelles
- 🎯 Augmente l'attractivité des événements
- 🌍 Support multilingue

---

### 2. ANALYSE DE SENTIMENT DES FEEDBACKS

#### 🎯 Objectif
Analyser automatiquement les feedbacks des participants pour identifier:
- Les événements les plus appréciés
- Les types d'événements préférés
- Les points d'amélioration
- Les tendances de satisfaction

#### 🔧 Choix du modèle

**Hugging Face - Sentiment Analysis (GRATUIT) ⭐⭐⭐⭐⭐**
- **Modèle**: `nlptown/bert-base-multilingual-uncased-sentiment`
- **Avantages**:
  - 100% gratuit
  - Multilingue (français + anglais)
  - Retourne un score 1-5 étoiles
  - Très précis pour les avis
- **API**: Hugging Face Inference API

#### 📦 Implémentation

**Étape 1: Créer l'entité Feedback**
```php
// src/Entity/Feedback.php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeedbackRepository;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Participation::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Participation $participation;

    #[ORM\Column(type: "integer")]
    private int $rating; // 1-5 étoiles

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    // Résultats de l'analyse AI
    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $sentimentLabel = null; // "very_positive", "positive", "neutral", "negative", "very_negative"

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $sentimentScore = null; // 0.0 - 1.0

    #[ORM\Column(type: "boolean")]
    private bool $isAnalyzed = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters / Setters...
}
```

**Étape 2: Créer le service SentimentAnalysisService**
```php
// src/Service/SentimentAnalysisService.php
<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Feedback;

class SentimentAnalysisService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        string $huggingfaceApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $huggingfaceApiKey;
    }

    /**
     * Analyse le sentiment d'un feedback
     */
    public function analyzeFeedback(Feedback $feedback): array
    {
        $comment = $feedback->getComment();
        
        if (!$comment || strlen($comment) < 10) {
            // Si pas de commentaire, utiliser seulement le rating
            return $this->analyzeFromRating($feedback->getRating());
        }

        try {
            $response = $this->httpClient->request('POST', 
                'https://api-inference.huggingface.co/models/nlptown/bert-base-multilingual-uncased-sentiment',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'inputs' => $comment,
                    ],
                    'timeout' => 15,
                ]
            );

            if ($response->getStatusCode() !== 200) {
                return $this->analyzeFromRating($feedback->getRating());
            }

            $data = $response->toArray();
            
            // Le modèle retourne un tableau de scores pour chaque label
            // Format: [{"label": "5 stars", "score": 0.95}, ...]
            $results = $data[0] ?? [];
            
            // Trouver le label avec le score le plus élevé
            $bestResult = null;
            $bestScore = 0;
            
            foreach ($results as $result) {
                if ($result['score'] > $bestScore) {
                    $bestScore = $result['score'];
                    $bestResult = $result;
                }
            }
            
            if (!$bestResult) {
                return $this->analyzeFromRating($feedback->getRating());
            }
            
            // Convertir le label (1-5 stars) en sentiment
            $stars = (int) $bestResult['label'][0]; // "5 stars" → 5
            
            return [
                'label' => $this->convertStarsToLabel($stars),
                'score' => $bestScore,
                'stars' => $stars,
            ];
            
        } catch (\Exception $e) {
            // En cas d'erreur, utiliser le rating manuel
            return $this->analyzeFromRating($feedback->getRating());
        }
    }

    /**
     * Analyse basée uniquement sur le rating manuel
     */
    private function analyzeFromRating(int $rating): array
    {
        return [
            'label' => $this->convertStarsToLabel($rating),
            'score' => $rating / 5.0,
            'stars' => $rating,
        ];
    }

    /**
     * Convertit un nombre d'étoiles en label de sentiment
     */
    private function convertStarsToLabel(int $stars): string
    {
        return match($stars) {
            5 => 'very_positive',
            4 => 'positive',
            3 => 'neutral',
            2 => 'negative',
            1 => 'very_negative',
            default => 'neutral',
        };
    }

    /**
     * Analyse tous les feedbacks d'un événement
     */
    public function analyzeEventFeedbacks(\App\Entity\Evenement $evenement): array
    {
        $feedbacks = [];
        $totalScore = 0;
        $count = 0;
        
        $sentimentCounts = [
            'very_positive' => 0,
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0,
            'very_negative' => 0,
        ];

        foreach ($evenement->getParticipations() as $participation) {
            // Récupérer le feedback de cette participation
            // (à implémenter: relation Participation → Feedback)
            // Pour l'instant, exemple:
            // $feedback = $participation->getFeedback();
            // if ($feedback) {
            //     $analysis = $this->analyzeFeedback($feedback);
            //     $sentimentCounts[$analysis['label']]++;
            //     $totalScore += $analysis['score'];
            //     $count++;
            // }
        }

        $averageScore = $count > 0 ? $totalScore / $count : 0;
        
        return [
            'average_score' => $averageScore,
            'total_feedbacks' => $count,
            'sentiment_distribution' => $sentimentCounts,
            'satisfaction_rate' => $averageScore * 100, // Pourcentage
        ];
    }
}
```

**Étape 3: Créer le système de Rating**

Pour le rating, pas besoin de bundle spécifique. On va créer un système simple et efficace:

```php
// Ajouter dans l'entité Feedback
#[ORM\Column(type: "json", nullable: true)]
private ?array $ratingDetails = null; // Détails du rating par catégorie

/**
 * Structure du ratingDetails:
 * {
 *   "organization": 4,  // Organisation (1-5)
 *   "content": 5,       // Contenu (1-5)
 *   "venue": 3,         // Lieu (1-5)
 *   "overall": 4        // Global (1-5)
 * }
 */
```

**Étape 4: Interface de feedback (Frontoffice)**
- Après la fin de l'événement, afficher un formulaire de feedback
- Rating par étoiles (JavaScript interactif)
- Champ commentaire optionnel
- Catégories de rating: Organisation, Contenu, Lieu, Global

**Valeur ajoutée:**
- 📊 Identification automatique des événements populaires
- 🎯 Aide à la décision: quels types d'événements organiser
- 📈 Suivi de la satisfaction dans le temps
- 🔍 Détection des problèmes récurrents

---

### 3. PRÉDICTION DU NOMBRE OPTIMAL DE PARTICIPANTS

#### 🎯 Objectif
Aider l'admin à définir le `nbMax` optimal pour éviter:
- ❌ nbMax trop bas → événement complet trop vite
- ❌ nbMax trop haut → événement vide, mauvaise image

#### 🔧 Approche: Machine Learning Simple (TensorFlow.js côté client)

**Pourquoi TensorFlow.js?**
- ✅ Gratuit et open-source
- ✅ Exécution côté client (pas de charge serveur)
- ✅ Pas besoin d'API externe
- ✅ Modèle simple et rapide

**Alternative: Régression linéaire côté serveur (PHP)**
- Plus simple à implémenter
- Pas besoin de JavaScript complexe
- Suffisant pour ce cas d'usage

#### 📦 Implémentation recommandée: Régression PHP (plus simple)

**Étape 1: Créer le service PredictionService**
```php
// src/Service/PredictionService.php
<?php

namespace App\Service;

use App\Repository\EvenementRepository;
use App\Entity\Evenement;

class PredictionService
{
    private EvenementRepository $evenementRepository;

    public function __construct(EvenementRepository $evenementRepository)
    {
        $this->evenementRepository = $evenementRepository;
    }

    /**
     * Prédit le nombre optimal de participants pour un événement
     */
    public function predictOptimalCapacity(
        string $type,
        \DateTimeInterface $dateDebut,
        ?string $lieu = null
    ): array {
        // Récupérer l'historique des événements similaires
        $historicalEvents = $this->getHistoricalEvents($type);
        
        if (count($historicalEvents) < 3) {
            // Pas assez de données, retourner des valeurs par défaut
            return $this->getDefaultPrediction($type);
        }

        // Calculer les statistiques
        $participationRates = [];
        $capacities = [];
        
        foreach ($historicalEvents as $event) {
            $nbMax = $event->getNbMax();
            $actualParticipants = count($event->getParticipations());
            
            $capacities[] = $nbMax;
            $participationRates[] = $actualParticipants / $nbMax;
        }

        // Calculer la moyenne et l'écart-type
        $avgRate = array_sum($participationRates) / count($participationRates);
        $avgCapacity = array_sum($capacities) / count($capacities);
        
        // Facteurs d'ajustement
        $dayOfWeekFactor = $this->getDayOfWeekFactor($dateDebut);
        $monthFactor = $this->getMonthFactor($dateDebut);
        
        // Prédiction
        $predictedRate = $avgRate * $dayOfWeekFactor * $monthFactor;
        $predictedCapacity = round($avgCapacity);
        
        // Calculer la capacité optimale
        $optimalCapacity = round($predictedCapacity / $predictedRate);
        
        // Limites de sécurité
        $optimalCapacity = max(5, min(100, $optimalCapacity));
        
        return [
            'predicted_capacity' => $optimalCapacity,
            'confidence' => $this->calculateConfidence(count($historicalEvents)),
            'historical_average' => round($avgCapacity),
            'participation_rate' => round($avgRate * 100, 1),
            'recommendation' => $this->getRecommendation($optimalCapacity, $avgCapacity),
        ];
    }

    /**
     * Récupère les événements historiques similaires
     */
    private function getHistoricalEvents(string $type): array
    {
        return $this->evenementRepository->createQueryBuilder('e')
            ->where('e.type = :type')
            ->andWhere('e.dateFin < :now')
            ->andWhere('e.isCanceled = false')
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateDebut', 'DESC')
            ->setMaxResults(10) // Derniers 10 événements
            ->getQuery()
            ->getResult();
    }

    /**
     * Facteur d'ajustement selon le jour de la semaine
     */
    private function getDayOfWeekFactor(\DateTimeInterface $date): float
    {
        $dayOfWeek = (int) $date->format('N'); // 1=Lundi, 7=Dimanche
        
        return match($dayOfWeek) {
            1, 2, 3, 4 => 1.0,  // Lundi-Jeudi: normal
            5 => 0.9,            // Vendredi: -10%
            6, 7 => 0.7,         // Weekend: -30%
            default => 1.0,
        };
    }

    /**
     * Facteur d'ajustement selon le mois
     */
    private function getMonthFactor(\DateTimeInterface $date): float
    {
        $month = (int) $date->format('n');
        
        return match($month) {
            1, 2 => 0.9,         // Janvier-Février: -10% (hiver)
            7, 8 => 0.7,         // Juillet-Août: -30% (vacances)
            9, 10, 11 => 1.1,    // Sept-Nov: +10% (rentrée)
            default => 1.0,
        };
    }

    /**
     * Calcule le niveau de confiance de la prédiction
     */
    private function calculateConfidence(int $historicalCount): string
    {
        if ($historicalCount >= 10) return 'high';
        if ($historicalCount >= 5) return 'medium';
        return 'low';
    }

    /**
     * Génère une recommandation textuelle
     */
    private function getRecommendation(int $predicted, float $historical): string
    {
        $diff = $predicted - $historical;
        
        if (abs($diff) < 5) {
            return "La capacité prédite est similaire à la moyenne historique.";
        } elseif ($diff > 0) {
            return "Nous recommandons d'augmenter la capacité de " . abs($diff) . " places par rapport à la moyenne.";
        } else {
            return "Vous pouvez réduire la capacité de " . abs($diff) . " places par rapport à la moyenne.";
        }
    }

    /**
     * Valeurs par défaut si pas assez de données
     */
    private function getDefaultPrediction(string $type): array
    {
        $defaults = [
            'Workshop' => 20,
            'Conference' => 50,
            'Hackathon' => 30,
            'Seminar' => 40,
        ];

        $capacity = $defaults[$type] ?? 25;

        return [
            'predicted_capacity' => $capacity,
            'confidence' => 'low',
            'historical_average' => $capacity,
            'participation_rate' => 80.0,
            'recommendation' => "Pas assez de données historiques. Valeur par défaut suggérée.",
        ];
    }
}
```

**Étape 2: Intégration dans le formulaire**
- Bouton "Prédire la capacité optimale" dans le formulaire
- Appel AJAX vers `/backoffice/evenement/predict-capacity`
- Afficher la prédiction avec niveau de confiance
- L'admin peut accepter ou modifier

**Valeur ajoutée:**
- 🎯 Optimisation automatique de la capacité
- 📊 Basé sur des données réelles
- ⏱️ Gain de temps pour l'admin
- 📈 Amélioration du taux de remplissage

---

