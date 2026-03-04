<?php

namespace App\Service;

use App\Repository\EvenementRepository;
use App\Entity\Evenement;

/**
 * Service pour analyser les feedbacks et calculer les statistiques
 */
class FeedbackAnalyticsService
{
    private EvenementRepository $evenementRepository;

    public function __construct(EvenementRepository $evenementRepository)
    {
        $this->evenementRepository = $evenementRepository;
    }

    /**
     * Analyse tous les feedbacks d'un événement
     */
    public function analyzeEventFeedbacks(Evenement $evenement): array
    {
        $allFeedbacks = [];
        $totalStudents = 0;
        
        // Récupérer tous les feedbacks de toutes les participations
        foreach ($evenement->getParticipations() as $participation) {
            $feedbacks = $participation->getFeedbacks();
            if ($feedbacks) {
                $allFeedbacks = array_merge($allFeedbacks, $feedbacks);
            }
            $totalStudents += $participation->getEquipe()->getEtudiants()->count();
        }

        if (empty($allFeedbacks)) {
            return [
                'has_feedbacks' => false,
                'total_students' => $totalStudents,
                'feedback_count' => 0,
            ];
        }

        return [
            'has_feedbacks' => true,
            'total_students' => $totalStudents,
            'feedback_count' => count($allFeedbacks),
            'response_rate' => round((count($allFeedbacks) / $totalStudents) * 100, 1),
            'ratings' => $this->calculateRatings($allFeedbacks),
            'sentiment_distribution' => $this->getSentimentDistribution($allFeedbacks),
            'comments' => $this->extractComments($allFeedbacks),
            'categories' => $this->analyzeCategoryRatings($allFeedbacks),
        ];
    }

    /**
     * Analyse les feedbacks par type d'événement
     * @param string|null $filterType Type d'événement à filtrer (conference, hackathon, workshop) ou null pour tous
     */
    public function analyzeByEventType(?string $filterType = null): array
    {
        // Charger les événements avec toutes les relations nécessaires (optimisé pour éviter N+1)
        $events = $this->evenementRepository->createQueryBuilder('e')
            ->leftJoin('e.participations', 'p')
            ->addSelect('p')
            ->leftJoin('p.equipe', 'eq')
            ->addSelect('eq')
            ->leftJoin('eq.etudiants', 'et')
            ->addSelect('et')
            ->getQuery()
            ->getResult();
        
        // Filtrer les événements si un type est spécifié
        if ($filterType) {
            $events = array_filter($events, function($event) use ($filterType) {
                return strtolower($event->getType()->value) === strtolower($filterType);
            });
        }
        
        $byType = [];

        foreach ($events as $event) {
            $type = $event->getType()->value;
            
            if (!isset($byType[$type])) {
                $byType[$type] = [
                    'count' => 0,
                    'total_feedbacks' => 0,
                    'total_rating' => 0,
                    'sentiment_counts' => [
                        'tres_satisfait' => 0,
                        'satisfait' => 0,
                        'neutre' => 0,
                        'decu' => 0,
                        'tres_decu' => 0,
                    ],
                ];
            }

            $analysis = $this->analyzeEventFeedbacks($event);
            
            if ($analysis['has_feedbacks']) {
                $byType[$type]['count']++;
                $byType[$type]['total_feedbacks'] += $analysis['feedback_count'];
                $byType[$type]['total_rating'] += $analysis['ratings']['global_average'];
                
                foreach ($analysis['sentiment_distribution'] as $sentiment => $count) {
                    $byType[$type]['sentiment_counts'][$sentiment] += $count;
                }
            }
        }

        // Calculer les moyennes
        foreach ($byType as $type => &$data) {
            if ($data['count'] > 0) {
                $data['average_rating'] = round($data['total_rating'] / $data['count'], 2);
                $data['satisfaction_rate'] = round(
                    (($data['sentiment_counts']['tres_satisfait'] + $data['sentiment_counts']['satisfait']) 
                    / $data['total_feedbacks']) * 100, 
                    1
                );
            }
        }

        return $byType;
    }

    /**
     * Calcule les ratings moyens
     */
    private function calculateRatings(array $feedbacks): array
    {
        $globalRatings = [];
        
        foreach ($feedbacks as $feedback) {
            $globalRatings[] = $feedback['rating_global'] ?? 3;
        }

        return [
            'global_average' => round(array_sum($globalRatings) / count($globalRatings), 2),
            'global_min' => min($globalRatings),
            'global_max' => max($globalRatings),
        ];
    }

    /**
     * Distribution des sentiments
     */
    private function getSentimentDistribution(array $feedbacks): array
    {
        $distribution = [
            'tres_satisfait' => 0,
            'satisfait' => 0,
            'neutre' => 0,
            'decu' => 0,
            'tres_decu' => 0,
        ];

        foreach ($feedbacks as $feedback) {
            $sentiment = $feedback['sentiment'] ?? 'neutre';
            if (isset($distribution[$sentiment])) {
                $distribution[$sentiment]++;
            }
        }

        return $distribution;
    }

    /**
     * Extrait tous les commentaires
     */
    private function extractComments(array $feedbacks): array
    {
        $comments = [];
        
        foreach ($feedbacks as $feedback) {
            if (!empty($feedback['comment'])) {
                $comments[] = [
                    'text' => $feedback['comment'],
                    'sentiment' => $feedback['sentiment'],
                    'rating' => $feedback['rating_global'] ?? 3,
                ];
            }
        }

        return $comments;
    }

    /**
     * Analyse les ratings par catégorie
     */
    private function analyzeCategoryRatings(array $feedbacks): array
    {
        $categories = [
            'organisation' => [],
            'contenu' => [],
            'lieu' => [],
            'animation' => [],
        ];

        foreach ($feedbacks as $feedback) {
            if (isset($feedback['rating_categories'])) {
                foreach ($feedback['rating_categories'] as $category => $rating) {
                    if (isset($categories[$category])) {
                        $categories[$category][] = $rating;
                    }
                }
            }
        }

        $result = [];
        foreach ($categories as $category => $ratings) {
            if (!empty($ratings)) {
                $result[$category] = [
                    'average' => round(array_sum($ratings) / count($ratings), 2),
                    'count' => count($ratings),
                ];
            }
        }

        return $result;
    }

    /**
     * Prépare les données pour l'AI (format structuré)
     * @param string|null $eventType Type d'événement à filtrer (conference, hackathon, workshop) ou null pour tous
     */
    public function prepareDataForAI(?string $eventType = null): array
    {
        $byType = $this->analyzeByEventType($eventType);
        
        // Charger les événements avec toutes les relations nécessaires (optimisé pour éviter N+1)
        $allEvents = $this->evenementRepository->createQueryBuilder('e')
            ->leftJoin('e.participations', 'p')
            ->addSelect('p')
            ->leftJoin('p.equipe', 'eq')
            ->addSelect('eq')
            ->leftJoin('eq.etudiants', 'et')
            ->addSelect('et')
            ->getQuery()
            ->getResult();
        
        // Filtrer les événements si un type est spécifié
        if ($eventType) {
            $allEvents = array_filter($allEvents, function($event) use ($eventType) {
                return strtolower($event->getType()->value) === strtolower($eventType);
            });
        }
        
        $recentComments = [];
        foreach ($allEvents as $event) {
            $analysis = $this->analyzeEventFeedbacks($event);
            if ($analysis['has_feedbacks']) {
                foreach ($analysis['comments'] as $comment) {
                    $recentComments[] = $comment['text'];
                }
            }
        }

        return [
            'by_type' => $byType,
            'total_events' => count($allEvents),
            'recent_comments' => array_slice($recentComments, -50), // 50 derniers commentaires
            'filter_type' => $eventType,
        ];
    }
}
