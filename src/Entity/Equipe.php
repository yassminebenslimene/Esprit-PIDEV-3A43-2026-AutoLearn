<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\EquipeRepository;
use App\Entity\Etudiant;
use App\Entity\Evenement;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message: "Le nom de l'équipe est obligatoire")]
    private string $nom;

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: "equipes")]
    #[ORM\JoinColumn(nullable: false)]
    private Evenement $evenement;

    #[ORM\ManyToMany(targetEntity: Etudiant::class)]
    #[ORM\JoinTable(name: "equipe_etudiant",
        joinColumns: [new ORM\JoinColumn(name: "equipe_id", referencedColumnName: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "etudiant_id", referencedColumnName: "userId")]
    )]
    #[Assert\Count(
        min: 4, max: 6, 
        minMessage: "Une équipe doit avoir au moins {{ limit }} étudiants",
        maxMessage: "Une équipe ne peut pas avoir plus de {{ limit }} étudiants"
    )]
    private Collection $etudiants;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
    }

    // ===== Getters / Setters =====
    public function getId(): ?int { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getEvenement(): Evenement { return $this->evenement; }
    public function setEvenement(Evenement $evenement): self { $this->evenement = $evenement; return $this; }

    public function getEtudiants(): Collection { return $this->etudiants; }

    public function addEtudiant(Etudiant $etudiant): self {
        if (!$this->etudiants->contains($etudiant)) $this->etudiants[] = $etudiant;
        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self {
        if ($this->etudiants->contains($etudiant)) $this->etudiants->removeElement($etudiant);
        return $this;
    }
}
