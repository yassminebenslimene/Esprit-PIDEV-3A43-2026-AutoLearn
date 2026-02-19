<?php

namespace App\Entity\GestionDeCours;

use App\Repository\Cours\ChapitreTraductionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChapitreTraductionRepository::class)]
#[ORM\Table(name: 'chapitre_traduction')]
#[ORM\Index(columns: ['chapitre_id', 'langue'], name: 'idx_chapitre_langue')]
class ChapitreTraduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Chapitre::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Chapitre $chapitre = null;

    #[ORM\Column(length: 5)]
    private ?string $langue = null; // 'fr', 'en', etc.

    #[ORM\Column(length: 500)]
    private ?string $titreTraduit = null;

    #[ORM\Column(type: 'text')]
    private ?string $contenuTraduit = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(?Chapitre $chapitre): static
    {
        $this->chapitre = $chapitre;
        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): static
    {
        $this->langue = $langue;
        return $this;
    }

    public function getTitreTraduit(): ?string
    {
        return $this->titreTraduit;
    }

    public function setTitreTraduit(string $titreTraduit): static
    {
        $this->titreTraduit = $titreTraduit;
        return $this;
    }

    public function getContenuTraduit(): ?string
    {
        return $this->contenuTraduit;
    }

    public function setContenuTraduit(string $contenuTraduit): static
    {
        $this->contenuTraduit = $contenuTraduit;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
