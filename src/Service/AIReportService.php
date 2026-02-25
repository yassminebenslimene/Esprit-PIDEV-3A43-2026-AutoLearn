<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service pour générer des rapports et recommandations via Mistral-7B
 */
class AIReportService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $model;
    private FeedbackAnalyticsService $analyticsService;

    public function __construct(
        HttpClientInterface $httpClient,
        FeedbackAnalyticsService $analyticsService,
        string $huggingfaceApiKey,
        string $huggingfaceModel
    ) {
        $this->httpClient = $httpClient;
        $this->analyticsService = $analyticsService;
        $this->apiKey = $huggingfaceApiKey;
        $this->model = $huggingfaceModel;
    }

    /**
     * Génère un rapport d'analyse complet via AI
     * @param string|null $eventType Type d'événement à filtrer (conference, hackathon, workshop) ou null pour tous
     */
    public function generateAnalysisReport(?string $eventType = null): ?string
    {
        try {
            $data = $this->analyticsService->prepareDataForAI($eventType);
            
            $prompt = $this->buildAnalysisPrompt($data, $eventType);
            
            return $this->callMistralAPI($prompt);
        } catch (\Exception $e) {
            error_log('Erreur generateAnalysisReport: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Génère des recommandations d'événements via AI
     * @param string|null $eventType Type d'événement à filtrer (conference, hackathon, workshop) ou null pour tous
     */
    public function generateEventRecommendations(?string $eventType = null): ?string
    {
        $data = $this->analyticsService->prepareDataForAI($eventType);
        
        $prompt = $this->buildRecommendationPrompt($data, $eventType);
        
        return $this->callMistralAPI($prompt);
    }

    /**
     * Génère des suggestions d'amélioration via AI
     * @param string|null $eventType Type d'événement à filtrer (conference, hackathon, workshop) ou null pour tous
     */
    public function generateImprovementSuggestions(?string $eventType = null): ?string
    {
        $data = $this->analyticsService->prepareDataForAI($eventType);
        
        $prompt = $this->buildImprovementPrompt($data, $eventType);
        
        return $this->callMistralAPI($prompt);
    }

    /**
     * Construit le prompt pour l'analyse
     */
    private function buildAnalysisPrompt(array $data, ?string $eventType = null): string
    {
        $byTypeText = $this->formatByTypeData($data['by_type']);
        $commentsText = $this->formatComments($data['recent_comments']);
        
        $filterInfo = $eventType ? "FILTRE ACTIF: Analyse uniquement pour les événements de type '{$eventType}'" : "Analyse globale de tous les types d'événements";
        
        // Date actuelle au format français
        $currentDate = (new \DateTime())->format('d/m/Y');

        return <<<PROMPT
Tu es un expert en analyse de données éducatives. Analyse les données suivantes et génère un rapport professionnel en français.

{$filterInfo}

DONNÉES DES ÉVÉNEMENTS:
{$byTypeText}

COMMENTAIRES RÉCENTS DES ÉTUDIANTS:
{$commentsText}

Génère un rapport d'analyse détaillé incluant:
1. Performance globale des événements {$this->getFilterSuffix($eventType)}
2. Types d'événements les plus appréciés (classement)
3. Analyse par catégorie (organisation, contenu, lieu, animation)
4. Tendances détectées dans les commentaires
5. Taux de satisfaction général

IMPORTANT - FORMAT DU RAPPORT:
- Commence par: "📊 RAPPORT D'ANALYSE DES ÉVÉNEMENTS ÉDUCATIFS"
- Puis: "📅 Date: {$currentDate}"
- Puis: "📌 Référence: Analyse des feedbacks étudiants et données quantitatives"
- Utilise des sections claires avec des titres en gras
- Présente les données sous forme de LISTE À PUCES (pas de tableau ASCII)
- Exemple de format pour les statistiques:
  
  📊 STATISTIQUES PAR TYPE D'ÉVÉNEMENT:
  
  🎯 Hackathon:
  • Note moyenne: 4.0/5 ⭐
  • Taux de satisfaction: 100%
  • Nombre de feedbacks: 3
  
  🎓 Conférence:
  • Note moyenne: 3.25/5 ⭐
  • Taux de satisfaction: 75%
  • Nombre de feedbacks: 4

Format: Rapport professionnel avec sections claires, émojis pour la lisibilité, et données chiffrées en LISTES À PUCES.
PROMPT;
    }

    /**
     * Construit le prompt pour les recommandations
     */
    private function buildRecommendationPrompt(array $data, ?string $eventType = null): string
    {
        $byTypeText = $this->formatByTypeData($data['by_type']);
        $commentsText = $this->formatComments($data['recent_comments']);
        
        $filterInfo = $eventType ? "FILTRE ACTIF: Recommandations uniquement pour les événements de type '{$eventType}'" : "Recommandations pour tous les types d'événements";

        return <<<PROMPT
Tu es un expert en planification d'événements éducatifs. Basé sur ces données, recommande 3 événements à organiser.

{$filterInfo}

DONNÉES DES ÉVÉNEMENTS PASSÉS:
{$byTypeText}

COMMENTAIRES DES ÉTUDIANTS:
{$commentsText}

Pour chaque événement recommandé, fournis:
1. Titre et type d'événement {$this->getFilterSuffix($eventType)}
2. Durée suggérée
3. Capacité recommandée (nombre d'équipes)
4. Justification détaillée (pourquoi cet événement?)
5. Satisfaction prédite (sur 5)
6. Sujets/thèmes suggérés basés sur les commentaires

Format: Liste numérotée, professionnelle, avec émojis et données chiffrées.
PROMPT;
    }

    /**
     * Construit le prompt pour les améliorations
     */
    private function buildImprovementPrompt(array $data, ?string $eventType = null): string
    {
        $byTypeText = $this->formatByTypeData($data['by_type']);
        $commentsText = $this->formatComments($data['recent_comments']);
        
        $filterInfo = $eventType ? "FILTRE ACTIF: Suggestions d'amélioration uniquement pour les événements de type '{$eventType}'" : "Suggestions d'amélioration pour tous les types d'événements";

        return <<<PROMPT
Tu es un consultant en amélioration continue pour des événements éducatifs. Analyse ces données et propose un plan d'amélioration.

{$filterInfo}

DONNÉES DES ÉVÉNEMENTS:
{$byTypeText}

COMMENTAIRES DES ÉTUDIANTS:
{$commentsText}

Génère un plan d'amélioration incluant:
1. Problèmes identifiés {$this->getFilterSuffix($eventType)} (classés par priorité: HAUTE, MOYENNE, BASSE)
2. Pour chaque problème:
   - Description du problème
   - Preuves (citations de commentaires si pertinent)
   - Actions recommandées (concrètes et applicables)
   - Impact estimé sur la satisfaction
3. Quick wins (améliorations rapides à implémenter)
4. Améliorations à long terme

Format: Plan structuré, professionnel, avec émojis et priorités claires.
PROMPT;
    }
    
    /**
     * Retourne un suffixe pour indiquer le filtre actif
     */
    private function getFilterSuffix(?string $eventType): string
    {
        return $eventType ? " (type: {$eventType})" : "";
    }

    /**
     * Appelle l'API Mistral via Hugging Face (nouvelle API router)
     */
    private function callMistralAPI(string $prompt): ?string
    {
        try {
            // Vérifier que la clé API est configurée
            if (empty($this->apiKey) || $this->apiKey === 'your_huggingface_token_here') {
                error_log('ERREUR: Clé API Hugging Face non configurée ou invalide');
                throw new \Exception('Clé API Hugging Face non configurée. Vérifiez HUGGINGFACE_API_KEY dans .env.local');
            }

            // Vérifier que le modèle est configuré
            if (empty($this->model)) {
                error_log('ERREUR: Modèle Hugging Face non configuré');
                throw new \Exception('Modèle Hugging Face non configuré. Vérifiez HUGGINGFACE_MODEL dans .env.local');
            }

            error_log('Appel API Mistral - Modèle: ' . $this->model);
            error_log('Token commence par: ' . substr($this->apiKey, 0, 7) . '...');

            // Utiliser la nouvelle API router avec format OpenAI-compatible
            $response = $this->httpClient->request('POST', 
                "https://router.huggingface.co/v1/chat/completions", 
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => $this->model . ':fastest', // Utiliser le provider le plus rapide
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt
                            ]
                        ],
                        'max_tokens' => 1500,
                        'temperature' => 0.7,
                    ],
                    'timeout' => 60,
                ]
            );

            $statusCode = $response->getStatusCode();
            error_log('Status Code API: ' . $statusCode);

            if ($statusCode !== 200) {
                $errorBody = $response->getContent(false);
                error_log('Erreur API Response: ' . $errorBody);
                
                // Messages d'erreur plus clairs
                if ($statusCode === 401) {
                    throw new \Exception('Token Hugging Face invalide ou expiré. Créez un nouveau token sur https://huggingface.co/settings/tokens avec la permission "Make calls to Inference Providers"');
                } elseif ($statusCode === 403) {
                    throw new \Exception('Accès refusé. Vérifiez que votre token a la permission "Make calls to Inference Providers"');
                } else {
                    throw new \Exception('API Hugging Face a retourné le code ' . $statusCode . ': ' . $errorBody);
                }
            }

            $data = $response->toArray();
            error_log('Réponse API reçue: ' . json_encode($data));
            
            // Extraire le texte généré (format OpenAI)
            $generatedText = $data['choices'][0]['message']['content'] ?? null;
            
            if (!$generatedText) {
                error_log('ERREUR: Aucun texte généré dans la réponse');
                throw new \Exception('Aucun texte généré par l\'API');
            }
            
            return trim($generatedText);
            
        } catch (\Exception $e) {
            // Log l'erreur détaillée
            error_log('ERREUR COMPLÈTE API Mistral: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            throw $e; // Relancer l'exception pour que le controller puisse la gérer
        }
    }

    /**
     * Formate les données par type pour le prompt
     */
    private function formatByTypeData(array $byType): string
    {
        $text = "";
        foreach ($byType as $type => $data) {
            if ($data['count'] > 0) {
                $text .= "- {$type}: {$data['average_rating']}/5 étoiles, ";
                $text .= "{$data['satisfaction_rate']}% de satisfaction, ";
                $text .= "{$data['total_feedbacks']} feedbacks\n";
            }
        }
        return $text ?: "Aucune donnée disponible";
    }

    /**
     * Formate les commentaires pour le prompt
     */
    private function formatComments(array $comments): string
    {
        if (empty($comments)) {
            return "Aucun commentaire disponible";
        }
        
        $text = "";
        foreach (array_slice($comments, 0, 20) as $i => $comment) {
            $text .= ($i + 1) . ". \"" . substr($comment, 0, 150) . "\"\n";
        }
        return $text;
    }
}
