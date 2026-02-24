<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\EvenementRepository;
use App\Enum\TypeEvenement;
use App\Enum\StatutEvenement;
use App\Entity\Equipe;
use App\Entity\Participation;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $titre;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le lieu doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le lieu ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $lieu;

    #[ORM\Column(type:"text")]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $description;

    #[ORM\Column(type:"string", enumType: TypeEvenement::class)]
    #[Assert\NotBlank(message: "Le type d'événement est obligatoire")]
    private TypeEvenement $type;

    #[ORM\Column(type:"datetime")]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    #[Assert\GreaterThan("today", message: "La date de début doit être dans le futur")]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type:"datetime")]
    #[Assert\NotBlank(message: "La date de fin est obligatoire")]
    #[Assert\Expression(
        "this.getDateFin() >= this.getDateDebut()",
        message: "La date de fin doit être après ou égale à la date de début"
    )]
    private \DateTimeInterface $dateFin;

    #[ORM\Column(type:"string", enumType: StatutEvenement::class)]
    private StatutEvenement $status = StatutEvenement::PLANIFIE;

    #[ORM\Column(type:"boolean")]
    private bool $isCanceled = false;
    
    // Propriété pour le Workflow Component
    #[ORM\Column(type:"string", length: 50)]
    private string $workflowStatus = 'planifie';

    #[ORM\Column(type:"integer")]
    #[Assert\NotBlank(message: "Le nombre maximum d'équipes est obligatoire")]
    #[Assert\Positive(message: "Le nombre maximum d'équipes doit être positif")]
    #[Assert\Range(
        min: 1,
        max: 100,
        notInRangeMessage: "Le nombre d'équipes doit être entre {{ min }} et {{ max }}"
    )]
    private int $nbMax;

    #[ORM\OneToMany(mappedBy: "evenement", targetEntity: Equipe::class)]
    private Collection $equipes;

    #[ORM\OneToMany(mappedBy: "evenement", targetEntity: Participation::class)]
    private Collection $participations;

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    // Met à jour le status automatiquement
    public function updateStatus(): void
    {
        $now = new \DateTime();
        
        // Si l'événement est annulé, le statut reste ANNULE
        if ($this->getIsCanceled()) {
            $this->setStatus(StatutEvenement::ANNULE);
        } 
        // Si la date/heure actuelle est après la date de fin
        elseif ($now > $this->getDateFin()) {
            $this->setStatus(StatutEvenement::PASSE);
        }
        // Si la date/heure actuelle est entre dateDebut et dateFin
        elseif ($now >= $this->getDateDebut() && $now <= $this->getDateFin()) {
            $this->setStatus(StatutEvenement::EN_COURS);
        } 
        // Si la date/heure actuelle est avant dateDebut
        elseif ($now < $this->getDateDebut()) {
            $this->setStatus(StatutEvenement::PLANIFIE);
        }
    }

    // ===== Getters / Setters =====
    public function getId(): ?int { return $this->id; }

    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getLieu(): string { return $this->lieu; }
    public function setLieu(string $lieu): self { $this->lieu = $lieu; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    public function getType(): TypeEvenement { return $this->type; }
    public function setType(TypeEvenement $type): self { $this->type = $type; return $this; }

    public function getDateDebut(): \DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): self { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFin(): \DateTimeInterface { return $this->dateFin; }
    public function setDateFin(\DateTimeInterface $dateFin): self { $this->dateFin = $dateFin; return $this; }

    public function getStatus(): StatutEvenement { return $this->status; }
    public function setStatus(StatutEvenement $status): self { $this->status = $status; return $this; }

    public function getIsCanceled(): bool { return $this->isCanceled; }
    public function setIsCanceled(bool $isCanceled): self { $this->isCanceled = $isCanceled; return $this; }
    
    public function getWorkflowStatus(): string { return $this->workflowStatus; }
    public function setWorkflowStatus(string $workflowStatus): self { 
        $this->workflowStatus = $workflowStatus;
        // Synchroniser avec l'enum StatutEvenement
        $this->syncStatusFromWorkflow();
        return $this;
    }
    
    // Synchronise le status (enum) avec le workflowStatus (string)
    private function syncStatusFromWorkflow(): void
    {
        match($this->workflowStatus) {
            'planifie' => $this->status = StatutEvenement::PLANIFIE,
            'en_cours' => $this->status = StatutEvenement::EN_COURS,
            'termine' => $this->status = StatutEvenement::PASSE,
            'annule' => $this->status = StatutEvenement::ANNULE,
            default => $this->status = StatutEvenement::PLANIFIE,
        };
    }

    public function getNbMax(): int { return $this->nbMax; }
    public function setNbMax(int $nbMax): self { $this->nbMax = $nbMax; return $this; }

    public function getEquipes(): Collection { return $this->equipes; }
    public function addEquipe(Equipe $equipe): self {
        if (!$this->equipes->contains($equipe)) $this->equipes[] = $equipe;
        return $this;
    }

    public function getParticipations(): Collection { return $this->participations; }
    public function addParticipation(Participation $p): self {
        if (!$this->participations->contains($p)) $this->participations[] = $p;
        return $this;
    }
    
    /**
     * Vérifie si les participations sont ouvertes pour cet événement
     * Les participations sont ouvertes uniquement si:
     * - L'événement est planifié (pas encore commencé)
     * - L'événement n'est pas annulé
     * - L'événement n'est pas terminé
     */
    public function areParticipationsOpen(): bool
    {
        // Vérifier le workflow status
        return $this->workflowStatus === 'planifie' && !$this->isCanceled;
    }
    
    /**
     * Vérifie si l'événement peut accepter de nouvelles participations
     * (alias de areParticipationsOpen pour plus de clarté)
     */
    public function canAcceptParticipations(): bool
    {
        return $this->areParticipationsOpen();
    }
}
