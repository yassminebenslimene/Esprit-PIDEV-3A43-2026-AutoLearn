<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Equipe;
use App\Entity\Participation;
use App\Enum\TypeEvenement;
use App\Enum\StatutEvenement;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    // ENUM TypeEvenement
    #[ORM\Column(enumType: TypeEvenement::class)]
    #[Assert\NotNull]
    private ?TypeEvenement $type = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    #[Assert\GreaterThan("today")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    #[Assert\GreaterThan(
        propertyPath: "dateDebut",
        message: "La date de fin doit être après la date de début"
    )]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $lieu = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $capaciteMax = null;

    // ENUM StatutEvenement
    #[ORM\Column(enumType: StatutEvenement::class)]
    #[Assert\NotNull]
    private ?StatutEvenement $statut = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCanceled = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Equipe::class, orphanRemoval: true)]
    private Collection $equipes;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Participation::class, orphanRemoval: true)]
    private Collection $participations;

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->statut = StatutEvenement::PLANIFIE;
    }

    // ================= GETTERS & SETTERS =================

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?TypeEvenement { return $this->type; }
    public function setType(TypeEvenement $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getCapaciteMax(): ?int { return $this->capaciteMax; }
    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;
        return $this;
    }

    public function getStatut(): ?StatutEvenement { return $this->statut; }
    public function setStatut(StatutEvenement $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function isCanceled(): bool { return $this->isCanceled; }
    public function setIsCanceled(bool $isCanceled): static
    {
        $this->isCanceled = $isCanceled;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    // ================= RELATIONS =================

    public function getEquipes(): Collection { return $this->equipes; }

    public function getParticipations(): Collection { return $this->participations; }
}
