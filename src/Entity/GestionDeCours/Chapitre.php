<?php

namespace App\Entity\GestionDeCours;

use App\Entity\Quiz;
use App\Repository\Cours\ChapitreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private string $titre;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le contenu du chapitre est obligatoire.')]
    #[Assert\Length(
        max: 10000,
        maxMessage: 'Le contenu ne doit pas dépasser {{ limit }} caractères.'
    )]
    private string $contenu;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ordre du chapitre est obligatoire.")]
    #[Assert\Positive(message: "L'ordre doit être un nombre positif.")]
    #[Assert\Type(type: 'integer', message: "L'ordre doit être un nombre entier.")]
    private int $ordre;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Les ressources ne doivent pas dépasser {{ limit }} caractères.'
    )]
    private ?string $ressources = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ressourceType = null; // 'lien' ou 'fichier'

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ressourceFichier = null; // Nom du fichier uploadé

    #[ORM\ManyToOne(inversedBy: 'chapitres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'chapitre', cascade: ['persist'])]
    private Collection $quizzes;

    /**
     * @var Collection<int, Ressource>
     */
    #[ORM\OneToMany(targetEntity: Ressource::class, mappedBy: 'chapitre', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $ressourcesMultiples;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->ressourcesMultiples = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getOrdre(): int
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

    public function getRessourceType(): ?string
    {
        return $this->ressourceType;
    }

    public function setRessourceType(?string $ressourceType): static
    {
        $this->ressourceType = $ressourceType;

        return $this;
    }

    public function getRessourceFichier(): ?string
    {
        return $this->ressourceFichier;
    }

    public function setRessourceFichier(?string $ressourceFichier): static
    {
        $this->ressourceFichier = $ressourceFichier;

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

    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setChapitre($this);
        }
        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            if ($quiz->getChapitre() === $this) {
                $quiz->setChapitre(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Ressource>
     */
    public function getRessourcesMultiples(): Collection
    {
        return $this->ressourcesMultiples;
    }

    public function addRessourcesMultiple(Ressource $ressource): static
    {
        if (!$this->ressourcesMultiples->contains($ressource)) {
            $this->ressourcesMultiples->add($ressource);
            $ressource->setChapitre($this);
        }
        return $this;
    }

    public function removeRessourcesMultiple(Ressource $ressource): static
    {
        if ($this->ressourcesMultiples->removeElement($ressource)) {
            if ($ressource->getChapitre() === $this) {
                $ressource->setChapitre(null);
            }
        }
        return $this;
    }
}
