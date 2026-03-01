<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide.")]
    private ?string $titre = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "La durée ne peut pas être vide.")]
    #[Assert\Positive(message: "La durée doit être un nombre positif.")]
    private ?int $duree = null; // Durée en minutes

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: "Le niveau ne peut pas être vide.")]
    #[Assert\Choice(choices: ["Débutant", "Intermédiaire", "Avancé"], message: "Le niveau doit être l'un des suivants : Débutant, Intermédiaire, Avancé.")]
    private ?string $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'challenges')]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "userId", nullable: false, onDelete: "CASCADE")]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, Exercice>
     */
    #[ORM\OneToMany(targetEntity: Exercice::class, mappedBy: 'challenge', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $exercices;

    /**
     * @var Collection<int, Quiz>
     */
    #[ORM\OneToMany(targetEntity: Quiz::class, mappedBy: 'challenge')]
    private Collection $quizzes;

    /**
     * @var Collection<int, UserChallenge>
     */
    #[ORM\OneToMany(targetEntity: UserChallenge::class, mappedBy: 'challenge')]
    private Collection $userChallenges;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'challenge', cascade: ["persist", "remove"])]
    private Collection $votes;

    public function __construct()
    {
        $this->exercices = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->userChallenges = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return Collection<int, Exercice>
     */
    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function addExercice(Exercice $exercice): static
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices[] = $exercice;
            $exercice->setChallenge($this);
        }
        return $this;
    }

    public function removeExercice(Exercice $exercice): static
    {
        if ($this->exercices->removeElement($exercice)) {
            if ($exercice->getChallenge() === $this) {
                $exercice->setChallenge(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): static
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setChallenge($this);
        }
        return $this;
    }

    public function removeQuiz(Quiz $quiz): static
    {
        if ($this->quizzes->removeElement($quiz)) {
            if ($quiz->getChallenge() === $this) {
                $quiz->setChallenge(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, UserChallenge>
     */
    public function getUserChallenges(): Collection
    {
        return $this->userChallenges;
    }

    public function addUserChallenge(UserChallenge $userChallenge): static
    {
        if (!$this->userChallenges->contains($userChallenge)) {
            $this->userChallenges->add($userChallenge);
            $userChallenge->setChallenge($this);
        }
        return $this;
    }

    public function removeUserChallenge(UserChallenge $userChallenge): static
    {
        if ($this->userChallenges->removeElement($userChallenge)) {
            if ($userChallenge->getChallenge() === $this) {
                $userChallenge->setChallenge(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function getNoteMoyenne(): float
    {
        $votes = $this->getVotes();
        if ($votes->count() === 0) {
            return 0;
        }
        
        $total = 0;
        foreach ($votes as $vote) {
            $total += $vote->getValeur();
        }
        
        return round($total / $votes->count(), 1);
    }

    public function getNombreVotes(): int
    {
        return $this->votes->count();
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setChallenge($this);
        }
        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            if ($vote->getChallenge() === $this) {
                $vote->setChallenge(null);
            }
        }
        return $this;
    }
}