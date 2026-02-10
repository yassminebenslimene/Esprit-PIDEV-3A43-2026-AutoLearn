<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\EtudiantTemp;
use App\Entity\Evenement;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
#[Assert\Callback('validate')]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $nom = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: "equipes")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Evenement $evenement = null;

    #[ORM\ManyToMany(targetEntity: EtudiantTemp::class)]
    private Collection $etudiants;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
    }

    // ================= GETTERS & SETTERS =================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(EtudiantTemp $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
        }

        return $this;
    }

    public function removeEtudiant(EtudiantTemp $etudiant): static
    {
        $this->etudiants->removeElement($etudiant);
        return $this;
    }

    public function getNombreMembres(): int
    {
        return $this->etudiants->count();
    }

    // ================= VALIDATION MÉTIER =================

    public function validate(ExecutionContextInterface $context): void
    {
        $nb = $this->getNombreMembres();

        if ($nb < 4) {
            $context->buildViolation("Une équipe doit avoir au moins 4 étudiants.")
                ->atPath('etudiants')
                ->addViolation();
        }

        if ($nb > 5) {
            $context->buildViolation("Une équipe ne peut pas avoir plus de 5 étudiants.")
                ->atPath('etudiants')
                ->addViolation();
        }
    }
}
