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
    private string $titre;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire")]
    private string $lieu;

    #[ORM\Column(type:"text")]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    private string $description;

    #[ORM\Column(type:"string", enumType: TypeEvenement::class)]
    #[Assert\NotBlank]
    private TypeEvenement $type;

    #[ORM\Column(type:"datetime")]
    #[Assert\NotBlank]
    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type:"datetime")]
    #[Assert\NotBlank]
    #[Assert\Expression(
        "this.getDateFin() >= this.getDateDebut()",
        message: "La date de fin doit être après la date de début"
    )]
    private \DateTimeInterface $dateFin;

    #[ORM\Column(type:"string", enumType: StatutEvenement::class)]
    private StatutEvenement $status = StatutEvenement::PLANIFIE;

    #[ORM\Column(type:"boolean")]
    private bool $isCanceled = false;

    #[ORM\Column(type:"integer")]
    #[Assert\Positive(message: "Le nombre maximum de participants doit être positif")]
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
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        $dateDebut = (clone $this->getDateDebut())->setTime(0, 0, 0);
        $dateFin = (clone $this->getDateFin())->setTime(0, 0, 0);

        // Si l'événement est annulé, le statut reste ANNULE
        if ($this->getIsCanceled()) {
            $this->setStatus(StatutEvenement::ANNULE);
        } 
        // Si la date d'aujourd'hui est entre dateDebut et dateFin (inclus)
        elseif ($today >= $dateDebut && $today <= $dateFin) {
            $this->setStatus(StatutEvenement::EN_COURS);
        } 
        // Si la date d'aujourd'hui est avant dateDebut
        elseif ($today < $dateDebut) {
            $this->setStatus(StatutEvenement::PLANIFIE);
        }
        // Si la date d'aujourd'hui est après dateFin, on garde le statut actuel
        // (l'événement est terminé mais on ne change pas le statut)
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
}
