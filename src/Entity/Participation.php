<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParticipationRepository;
use App\Enum\StatutParticipation;
use App\Entity\Equipe;
use App\Entity\Evenement;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Equipe $equipe;

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: "participations")]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\BatchFetch(size: 10)]
    private Evenement $evenement;

    #[ORM\Column(length:50, enumType: StatutParticipation::class)]
    private StatutParticipation $statut = StatutParticipation::EN_ATTENTE;

    /**
     * Feedbacks des étudiants de l'équipe (JSON)
     * Structure: [
     *   {
     *     "etudiant_id": 123,
     *     "etudiant_name": "Ahmed Ben Ali",
     *     "rating_global": 5,                    // Rating global (1-5 étoiles)
     *     "rating_categories": {                 // Ratings par catégorie
     *       "organisation": 5,
     *       "contenu": 4,
     *       "lieu": 3,
     *       "animation": 5
     *     },
     *     "sentiment": "tres_satisfait",         // Sentiment choisi
     *     "emoji": "😍",                         // Emoji correspondant
     *     "comment": "Super événement!",         // Commentaire libre
     *     "created_at": "2026-02-20 14:30:00"
     *   }
     * ]
     */
    #[ORM\Column(type: "json", nullable: true)]
    private ?array $feedbacks = null;

    /**
     * Validation automatique de la participation
     * Retourne un tableau avec 'accepted' (bool) et 'message' (string)
     */
    public function validateParticipation(): array
    {
        $evenement = $this->getEvenement();
        $equipe = $this->getEquipe();

        // 1. Vérifier si l'événement est annulé
        if ($evenement->getIsCanceled()) {
            $this->setStatut(StatutParticipation::REFUSE);
            return [
                'accepted' => false,
                'message' => 'L\'événement "' . $evenement->getTitre() . '" a été annulé. Aucune participation n\'est acceptée.'
            ];
        }

        // 2. Vérification nbMax - ne compter que les participations ACCEPTÉES (sauf celle-ci)
        $acceptedCount = 0;
        foreach ($evenement->getParticipations() as $p) {
            // Ne pas compter la participation actuelle
            if ($p->getId() === $this->getId()) {
                continue;
            }
            if ($p->getStatut() === StatutParticipation::ACCEPTE) {
                $acceptedCount++;
            }
        }
        
        if ($acceptedCount >= $evenement->getNbMax()) {
            $this->setStatut(StatutParticipation::REFUSE);
            return [
                'accepted' => false,
                'message' => 'La capacité maximale de l\'événement est atteinte (' . $evenement->getNbMax() . ' équipes maximum). Votre participation a été refusée.'
            ];
        }

        // 3. Vérification doublon étudiants - ne vérifier que les autres participations ACCEPTÉES
        foreach ($evenement->getParticipations() as $p) {
            // Ne pas vérifier contre soi-même
            if ($p->getId() === $this->getId()) {
                continue;
            }
            
            // Vérifier uniquement les participations acceptées
            if ($p->getStatut() !== StatutParticipation::ACCEPTE) {
                continue;
            }
            
            foreach ($p->getEquipe()->getEtudiants() as $etudiant) {
                foreach ($equipe->getEtudiants() as $membre) {
                    if ($etudiant->getId() === $membre->getId()) {
                        $this->setStatut(StatutParticipation::REFUSE);
                        return [
                            'accepted' => false,
                            'message' => 'L\'étudiant "' . $membre->getPrenom() . ' ' . $membre->getNom() . '" participe déjà à cet événement avec l\'équipe "' . $p->getEquipe()->getNom() . '". Un étudiant ne peut pas participer avec deux équipes différentes au même événement.'
                        ];
                    }
                }
            }
        }

        // Tout est OK - accepter la participation
        $this->setStatut(StatutParticipation::ACCEPTE);
        return [
            'accepted' => true,
            'message' => 'Participation acceptée avec succès ! Votre équipe "' . $equipe->getNom() . '" est inscrite à l\'événement "' . $evenement->getTitre() . '".'
        ];
    }

    // ===== Getters / Setters =====
    public function getId(): ?int { return $this->id; }

    public function getEquipe(): Equipe { return $this->equipe; }
    public function setEquipe(Equipe $equipe): self { $this->equipe = $equipe; return $this; }

    public function getEvenement(): Evenement { return $this->evenement; }
    public function setEvenement(Evenement $evenement): self { $this->evenement = $evenement; return $this; }

    public function getStatut(): StatutParticipation { return $this->statut; }
    public function setStatut(StatutParticipation $statut): self { $this->statut = $statut; return $this; }

    // ===== Gestion des Feedbacks =====
    
    public function getFeedbacks(): ?array { return $this->feedbacks; }
    public function setFeedbacks(?array $feedbacks): self { $this->feedbacks = $feedbacks; return $this; }

    /**
     * Ajoute un feedback d'un étudiant avec tous les détails
     */
    public function addFeedback(
        int $etudiantId,
        string $etudiantName,
        int $ratingGlobal,              // Rating global (1-5)
        array $ratingCategories,        // ['organisation' => 5, 'contenu' => 4, ...]
        string $sentiment,              // 'tres_satisfait', 'satisfait', etc.
        string $emoji,                  // '😍', '😊', etc.
        ?string $comment = null
    ): self {
        if ($this->feedbacks === null) {
            $this->feedbacks = [];
        }

        // Vérifier si l'étudiant a déjà donné un feedback
        foreach ($this->feedbacks as $key => $feedback) {
            if ($feedback['etudiant_id'] === $etudiantId) {
                // Remplacer le feedback existant
                $this->feedbacks[$key] = [
                    'etudiant_id' => $etudiantId,
                    'etudiant_name' => $etudiantName,
                    'rating_global' => $ratingGlobal,
                    'rating_categories' => $ratingCategories,
                    'sentiment' => $sentiment,
                    'emoji' => $emoji,
                    'comment' => $comment,
                    'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ];
                return $this;
            }
        }

        // Ajouter un nouveau feedback
        $this->feedbacks[] = [
            'etudiant_id' => $etudiantId,
            'etudiant_name' => $etudiantName,
            'rating_global' => $ratingGlobal,
            'rating_categories' => $ratingCategories,
            'sentiment' => $sentiment,
            'emoji' => $emoji,
            'comment' => $comment,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        return $this;
    }

    /**
     * Récupère le feedback d'un étudiant spécifique
     */
    public function getFeedbackByEtudiant(int $etudiantId): ?array
    {
        if ($this->feedbacks === null) {
            return null;
        }

        foreach ($this->feedbacks as $feedback) {
            if ($feedback['etudiant_id'] === $etudiantId) {
                return $feedback;
            }
        }

        return null;
    }

    /**
     * Vérifie si un étudiant a déjà donné un feedback
     */
    public function hasFeedbackFromEtudiant(int $etudiantId): bool
    {
        return $this->getFeedbackByEtudiant($etudiantId) !== null;
    }

    /**
     * Compte le nombre de feedbacks reçus
     */
    public function getFeedbackCount(): int
    {
        return $this->feedbacks ? count($this->feedbacks) : 0;
    }

    /**
     * Calcule le score moyen des feedbacks (1-5)
     */
    public function getAverageFeedbackScore(): ?float
    {
        if (!$this->feedbacks || count($this->feedbacks) === 0) {
            return null;
        }

        $totalScore = 0;
        foreach ($this->feedbacks as $feedback) {
            // Convertir le sentiment en score
            $score = match($feedback['sentiment']) {
                'tres_satisfait' => 5,
                'satisfait' => 4,
                'neutre' => 3,
                'decu' => 2,
                'tres_decu' => 1,
                default => 3,
            };
            $totalScore += $score;
        }

        return round($totalScore / count($this->feedbacks), 2);
    }

    /**
     * Retourne la distribution des sentiments
     */
    public function getSentimentDistribution(): array
    {
        $distribution = [
            'tres_satisfait' => 0,
            'satisfait' => 0,
            'neutre' => 0,
            'decu' => 0,
            'tres_decu' => 0,
        ];

        if (!$this->feedbacks) {
            return $distribution;
        }

        foreach ($this->feedbacks as $feedback) {
            $sentiment = $feedback['sentiment'] ?? 'neutre';
            if (isset($distribution[$sentiment])) {
                $distribution[$sentiment]++;
            }
        }

        return $distribution;
    }

    /**
     * Vérifie si tous les membres de l'équipe ont donné leur feedback
     */
    public function hasAllFeedbacks(): bool
    {
        $nbEtudiants = $this->getEquipe()->getEtudiants()->count();
        $nbFeedbacks = $this->getFeedbackCount();
        
        return $nbFeedbacks >= $nbEtudiants;
    }
}
