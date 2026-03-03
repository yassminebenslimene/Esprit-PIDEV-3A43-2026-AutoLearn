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
        $maxRetries = 2;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                $attempt++;
                
                // Vérifier si Groq est disponible
                if (!$this->groqService->isAvailable()) {
                    $this->logger->warning('Groq API not available for exercise generation');
                    throw new \Exception('Le service de génération IA n\'est pas disponible actuellement.');
                }

                $prompt = $this->buildGenerationPrompt($sujet, $niveau, $nombre);
                
                $messages = [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un expert pédagogique qui crée des exercices éducatifs de TRÈS HAUTE QUALITÉ. RÈGLE ABSOLUE: Chaque réponse doit faire MINIMUM 3-5 phrases complètes (150-300 caractères). JAMAIS de réponses courtes! Tes réponses doivent être aussi détaillées que des explications de manuel scolaire. Tu réponds UNIQUEMENT en JSON valide, SANS AUCUN TEXTE AVANT OU APRÈS le JSON. Commence directement par { et termine par }.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ];

                $response = $this->groqService->chat($messages, [
                    'temperature' => 0.9,  // Plus créatif et verbeux
                    'max_tokens' => 4000   // Plus d'espace pour réponses longues
                ]);

                if (!$response) {
                    $this->logger->error('No response from Groq API for exercise generation');
                    if ($attempt < $maxRetries) {
                        $this->logger->info("Retrying... Attempt $attempt/$maxRetries");
                        sleep(1); // Wait 1 second before retry
                        continue;
                    }
                    throw new \Exception('Aucune réponse de l\'IA. Veuillez réessayer.');
                }

                // Parser la réponse de l'IA
                $exercices = $this->parseAIResponse($response);
                
                if (empty($exercices)) {
                    $this->logger->error('Failed to parse AI response for exercises', [
                        'response' => substr($response, 0, 500),
                        'attempt' => $attempt
                    ]);
                    if ($attempt < $maxRetries) {
                        $this->logger->info("Retrying... Attempt $attempt/$maxRetries");
                        sleep(1);
                        continue;
                    }
                    throw new \Exception('Impossible de générer les exercices. Format de réponse invalide.');
                }

                return $exercices;

            } catch (\Exception $e) {
                $this->logger->error('Error in exercise generation', [
                    'message' => $e->getMessage(),
                    'attempt' => $attempt,
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($attempt >= $maxRetries) {
                    throw $e;
                }
                
                sleep(1); // Wait before retry
            }
        }
        
        throw new \Exception('Impossible de générer les exercices après plusieurs tentatives.');
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
Tu dois générer {$nombre} exercices pédagogiques COMPLETS sur: "{$sujet}"

**Niveau:** {$niveauDescription}
**Points:** {$pointsRange}

🚨 RÈGLE ABSOLUE POUR LES RÉPONSES 🚨

CHAQUE RÉPONSE DOIT CONTENIR AU MINIMUM:
✅ 3-5 PHRASES COMPLÈTES (pas 1 ou 2!)
✅ 150-300 CARACTÈRES MINIMUM
✅ Explication détaillée du concept
✅ Exemples concrets si possible
✅ Contexte et utilité

❌ INTERDIT:
❌ Réponses d'un seul mot: "Une variable"
❌ Réponses d'une seule phrase courte
❌ Réponses vagues ou incomplètes
❌ Réponses sans explication

📚 EXEMPLES OBLIGATOIRES À SUIVRE:

EXEMPLE 1 - Sujet: Python
Question: "Qu'est-ce qu'une variable en Python?"

❌ MAUVAISE RÉPONSE (TROP COURTE):
"Une variable est un conteneur pour stocker des données."

✅ BONNE RÉPONSE (À SUIVRE):
"Une variable en Python est un conteneur qui permet de stocker une valeur en mémoire sous un nom symbolique. Elle est créée automatiquement lors de la première affectation avec l'opérateur égal (=). Python utilise un typage dynamique, ce qui signifie que le type de la variable est déterminé automatiquement selon la valeur assignée, sans besoin de déclaration explicite. Par exemple, 'x = 5' crée une variable entière, tandis que 'nom = \"Alice\"' crée une variable de type chaîne de caractères."

EXEMPLE 2 - Sujet: Java
Question: "Comment fonctionne une boucle for en Java?"

❌ MAUVAISE RÉPONSE (TROP COURTE):
"Une boucle for permet de répéter du code."

✅ BONNE RÉPONSE (À SUIVRE):
"Une boucle for en Java est une structure de contrôle qui permet d'exécuter un bloc de code un nombre déterminé de fois. Elle se compose de trois parties: l'initialisation d'un compteur, la condition de continuation, et l'incrémentation du compteur. La syntaxe est: for(int i=0; i<10; i++) { ... }. Cette boucle est particulièrement utile lorsqu'on connaît à l'avance le nombre d'itérations nécessaires, comme pour parcourir un tableau ou répéter une action un nombre fixe de fois. Elle est plus concise que la boucle while pour ce type d'usage."

EXEMPLE 3 - Sujet: Bases de données
Question: "Qu'est-ce qu'une clé primaire?"

❌ MAUVAISE RÉPONSE (TROP COURTE):
"Une clé primaire identifie une ligne."

✅ BONNE RÉPONSE (À SUIVRE):
"Une clé primaire (Primary Key) est un champ ou une combinaison de champs dans une table de base de données qui identifie de manière unique chaque enregistrement. Elle ne peut pas contenir de valeurs NULL et doit être unique pour chaque ligne de la table. La clé primaire est essentielle pour maintenir l'intégrité des données et établir des relations entre les tables via les clés étrangères. Par exemple, dans une table 'Utilisateurs', le champ 'id_utilisateur' sert souvent de clé primaire car chaque utilisateur a un identifiant unique. Elle permet également d'optimiser les performances des requêtes de recherche."

🎯 TON OBJECTIF:
Génère {$nombre} exercices où CHAQUE réponse est aussi LONGUE et DÉTAILLÉE que les exemples ci-dessus!

**FORMAT DE RÉPONSE OBLIGATOIRE (JSON uniquement):**

⚠️ CRITIQUE: Réponds UNIQUEMENT avec le JSON ci-dessous, SANS AUCUN TEXTE AVANT OU APRÈS!
⚠️ PAS de "Voici les exercices:", PAS d'explication, JUSTE LE JSON!

{
    "exercices": [
        {
            "question": "Question claire et précise ici",
            "reponse": "Réponse LONGUE et DÉTAILLÉE de 3-5 phrases minimum (150-300 caractères) avec explication complète, exemples et contexte comme dans les exemples ci-dessus",
            "points": 10
        }
    ]
}

⚠️ RAPPEL: Commence ta réponse directement par { et termine par }

**VALIDATION AVANT D'ENVOYER:**
- ✅ Chaque réponse fait au moins 3-5 phrases?
- ✅ Chaque réponse fait au moins 150 caractères?
- ✅ Chaque réponse explique le concept en détail?
- ✅ Chaque réponse ressemble aux exemples donnés?

**IMPORTANT:**
- Génère EXACTEMENT {$nombre} exercices
- Points dans la fourchette {$pointsRange}
- Réponds UNIQUEMENT en JSON valide
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
        
        // Essayer d'extraire le JSON s'il y a du texte avant/après
        if (preg_match('/\{[\s\S]*"exercices"[\s\S]*\}/i', $response, $matches)) {
            $response = $matches[0];
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('JSON decode error in exercise generation', [
                'error' => json_last_error_msg(),
                'response' => substr($response, 0, 500) // Log first 500 chars
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
            
            // Vérifier que la réponse n'est pas trop courte
            $reponse = trim($exercice['reponse']);
            $reponseLength = strlen($reponse);
            
            if ($reponseLength < 100) {
                $this->logger->warning('Skipping exercise with too short answer', [
                    'question' => $exercice['question'],
                    'reponse' => $reponse,
                    'length' => $reponseLength
                ]);
                continue;
            }

            $exercices[] = [
                'question' => trim($exercice['question']),
                'reponse' => $reponse,
                'points' => max(1, min(100, (int) $exercice['points']))
            ];
        }

        return $exercices;
    }
}
