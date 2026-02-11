<?php

namespace App\Entity;

use App\Repository\ChapitreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChapitreRepository::class)]
class Chapitre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre du chapitre est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s\-_\'éèêëàâäîïôöûüç]+$/',
        message: 'Le titre contient des caractères non autorisés.'
    )]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le contenu du chapitre est obligatoire.')]
    #[Assert\Length(
        max: 10000,
        maxMessage: 'Le contenu ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $contenu = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ordre du chapitre est obligatoire.")]
    #[Assert\Positive(message: "L'ordre doit être un nombre positif.")]
    #[Assert\Type(type: 'integer', message: "L'ordre doit être un nombre entier.")]
    private ?int $ordre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Les ressources ne doivent pas dépasser {{ limit }} caractères.'
    )]
    private ?string $ressources = null;

    #[ORM\ManyToOne(inversedBy: 'chapitres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getRessources(): ?string
    {
        return $this->ressources;
    }

    public function setRessources(?string $ressources): static
    {
        $this->ressources = $ressources;

        return $this;
    }

    public function getCours(): ?Cours // <-- CORRECTION : 'C' majuscule
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static // <-- CORRECTION : 'C' majuscule
    {
        $this->cours = $cours;

        return $this;
    }
}