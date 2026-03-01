<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class ExerciceGeneratorAIService
{
    private GroqService $groqService;
    private LoggerInterface $logger;

    public function __construct(GroqService $groqService, LoggerInterface $logger)
    {
        $this->groqService = $groqService;
        $this->logger = $logger;
    }

    /**
     * Génère automatiquement des exercices en utilisant l'IA Groq
     * 
     * @param string $sujet Le sujet des exercices à générer
     * @param string $niveau Le niveau de difficulté (Débutant, Intermédiaire, Avancé)
     * @param int $nombre Nombre d'exercices à générer (par défaut 5)
     * @return array Liste des exercices générés avec question, réponse et points
     */
    public function generateExercices(string $sujet, string $niveau, int $nombre = 5): array
    {
        try {
            // Vérifier si Groq est disponible
            if (!$this->groqService->isAvailable()) {
                $this->logger->warning('Groq API not available for exercise generation');
                throw new \Exception('Le service de génération IA n\'est pas disponible actuellement.');
            }

            $prompt = $this->buildGenerationPrompt($sujet, $niveau, $nombre);
            
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Tu es un expert pédagogique qui crée des exercices éducatifs de haute qualité. Tu génères des questions claires et des réponses COMPLÈTES et DÉTAILLÉES (minimum 2-3 phrases). Tes réponses expliquent toujours les concepts en profondeur. Tu réponds UNIQUEMENT en JSON valide.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            $response = $this->groqService->chat($messages, [
                'temperature' => 0.8,
                'max_tokens' => 3000
            ]);

            if (!$response) {
                $this->logger->error('No response from Groq API for exercise generation');
                throw new \Exception('Aucune réponse de l\'IA. Veuillez réessayer.');
            }

            // Parser la réponse de l'IA
            $exercices = $this->parseAIResponse($response);
            
            if (empty($exercices)) {
                $this->logger->error('Failed to parse AI response for exercises', ['response' => $response]);
                throw new \Exception('Impossible de générer les exercices. Format de réponse invalide.');
            }

            return $exercices;

        } catch (\Exception $e) {
            $this->logger->error('Error in exercise generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Construit le prompt pour la génération d'exercices
     */
    private function buildGenerationPrompt(string $sujet, string $niveau, int $nombre): string
    {
        $niveauDescription = match($niveau) {
            'Débutant' => 'niveau débutant (questions simples et claires, concepts de base, réponses courtes et précises)',
            'Intermédiaire' => 'niveau intermédiaire (questions moyennement complexes, concepts avancés, réponses détaillées)',
            'Avancé' => 'niveau avancé (questions complexes et approfondies, concepts experts, réponses complètes et techniques)',
            default => 'niveau intermédiaire'
        };

        $pointsRange = match($niveau) {
            'Débutant' => '5-10 points',
            'Intermédiaire' => '10-15 points',
            'Avancé' => '15-20 points',
            default => '10-15 points'
        };

        return <<<PROMPT
Génère exactement {$nombre} exercices pédagogiques de qualité sur le sujet: "{$sujet}"

**Niveau de difficulté:** {$niveauDescription}
**Points par exercice:** {$pointsRange}

**RÈGLES IMPORTANTES POUR LES QUESTIONS:**
1. Questions claires, précises et sans ambiguïté
2. Questions variées couvrant différents aspects du sujet
3. Complexité adaptée au niveau demandé
4. Formulation professionnelle et pédagogique

**RÈGLES CRITIQUES POUR LES RÉPONSES:**
1. ✅ Réponses COMPLÈTES et DÉTAILLÉES (minimum 2-3 phrases)
2. ✅ Réponses PRÉCISES avec tous les éléments clés
3. ✅ Réponses COMPRÉHENSIBLES qui expliquent le concept
4. ❌ PAS de réponses courtes type "oui/non" ou un seul mot
5. ❌ PAS de réponses vagues ou incomplètes

**EXEMPLES DE BONNES RÉPONSES:**

❌ MAUVAIS: "Télécharger Python"
✅ BON: "Pour installer Python, il faut d'abord télécharger l'installateur officiel depuis le site python.org, puis l'exécuter en cochant l'option 'Add Python to PATH' pour pouvoir l'utiliser depuis n'importe quel terminal."

❌ MAUVAIS: "Une boucle for"
✅ BON: "Une boucle for en Python permet de parcourir une séquence (liste, tuple, chaîne) élément par élément. Elle s'écrit 'for element in sequence:' et exécute le bloc de code indenté pour chaque élément de la séquence."

❌ MAUVAIS: "C'est une fonction"
✅ BON: "Une fonction en programmation est un bloc de code réutilisable qui effectue une tâche spécifique. Elle peut accepter des paramètres en entrée et retourner un résultat. On la définit avec le mot-clé 'def' suivi du nom et des paramètres entre parenthèses."

**FORMAT DE RÉPONSE OBLIGATOIRE (JSON uniquement):**
{
    "exercices": [
        {
            "question": "Question claire et précise ici (une phrase interrogative complète)",
            "reponse": "Réponse COMPLÈTE et DÉTAILLÉE ici (minimum 2-3 phrases explicatives avec tous les détails importants)",
            "points": 10
        }
    ]
}

**VALIDATION AVANT D'ENVOYER:**
- ✅ Chaque réponse fait au moins 2-3 phrases complètes?
- ✅ Chaque réponse explique clairement le concept?
- ✅ Chaque réponse contient tous les éléments clés?
- ✅ Les réponses sont compréhensibles et pédagogiques?

**IMPORTANT:**
- Génère EXACTEMENT {$nombre} exercices
- Réponds UNIQUEMENT en JSON valide, sans texte avant ou après
- Les réponses doivent être COMPLÈTES et DÉTAILLÉES
- Points dans la fourchette {$pointsRange}
PROMPT;
    }

    /**
     * Parse la réponse de l'IA en tableau d'exercices
     */
    private function parseAIResponse(string $response): array
    {
        // Nettoyer la réponse
        $response = trim($response);
        
        // Supprimer les blocs markdown si présents
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*$/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('JSON decode error in exercise generation', [
                'error' => json_last_error_msg(),
                'response' => $response
            ]);
            return [];
        }

        // Valider la structure
        if (!isset($data['exercices']) || !is_array($data['exercices'])) {
            $this->logger->error('Invalid structure in AI response', ['data' => $data]);
            return [];
        }

        $exercices = [];
        foreach ($data['exercices'] as $exercice) {
            // Valider chaque exercice
            if (!isset($exercice['question']) || !isset($exercice['reponse']) || !isset($exercice['points'])) {
                $this->logger->warning('Skipping invalid exercise', ['exercice' => $exercice]);
                continue;
            }

            $exercices[] = [
                'question' => trim($exercice['question']),
                'reponse' => trim($exercice['reponse']),
                'points' => max(1, min(100, (int) $exercice['points']))
            ];
        }

        return $exercices;
    }
}
