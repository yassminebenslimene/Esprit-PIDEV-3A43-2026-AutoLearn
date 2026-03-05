<?php 

namespace App\Entity;

use App\Entity\GestionDeCours\Chapitre;
use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
#[Vich\Uploadable]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du quiz est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9àâäéèêëïîôùûüÿçÀÂÄÉÈÊËÏÎÔÙÛÜŸÇ\s\-',.!?]+$/u",
        message: "Le titre contient des caractères non autorisés."
    )]
    private string $titre;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private string $description;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "L'état du quiz est obligatoire.")]
    #[Assert\Choice(
        choices: ['actif', 'inactif', 'brouillon', 'archive'],
        message: "L'état doit être: actif, inactif, brouillon ou archive."
    )]
    private string $etat = 'brouillon';

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "La durée doit être un nombre positif.")]
    private ?int $dureeMaxMinutes = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "Le seuil de réussite doit être entre {{ min }}% et {{ max }}%."
    )]
    private ?int $seuilReussite = 50;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "Le nombre de tentatives doit être positif.")]
    private ?int $maxTentatives = null;

    #[Vich\UploadableField(mapping: 'quiz_images', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Chapitre $chapitre = null;

    #[ORM\ManyToOne(inversedBy: 'quizzes')]
    #[ORM\JoinColumn(name: 'challenge_id', referencedColumnName: 'id', nullable: true)]
    private ?Challenge $challenge = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDureeMaxMinutes(): ?int
    {
        return $this->dureeMaxMinutes;
    }

    public function setDureeMaxMinutes(?int $dureeMaxMinutes): static
    {
        $this->dureeMaxMinutes = $dureeMaxMinutes;
        return $this;
    }

    public function getSeuilReussite(): ?int
    {
        return $this->seuilReussite;
    }

    public function setSeuilReussite(?int $seuilReussite): static
    {
        $this->seuilReussite = $seuilReussite;
        return $this;
    }

    public function getMaxTentatives(): ?int
    {
        return $this->maxTentatives;
    }

    public function setMaxTentatives(?int $maxTentatives): static
    {
        $this->maxTentatives = $maxTentatives;
        return $this;
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

    public function getChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setChallenge(?Challenge $challenge): static
    {
        $this->challenge = $challenge;
        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }
        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }
        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}