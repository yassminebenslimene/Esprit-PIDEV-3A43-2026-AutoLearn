<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le texte de la question est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: "La question doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La question ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $texteQuestion = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de points est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de points doit être positif.")]
    #[Assert\Range(
        min: 1,
        max: 100,
        notInRangeMessage: "Le nombre de points doit être entre {{ min }} et {{ max }}."
    )]
    #[Assert\Type(
        type: 'integer',
        message: "Le nombre de points doit être un nombre entier."
    )]
    private ?int $point = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La question doit être associée à un quiz.")]
    private ?Quiz $quiz = null;

    /**
     * @var Collection<int, Option>
     */
    #[ORM\OneToMany(targetEntity: Option::class, mappedBy: 'question', orphanRemoval: true)]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexteQuestion(): ?string
    {
        return $this->texteQuestion;
    }

    public function setTexteQuestion(string $texteQuestion): static
    {
        $this->texteQuestion = $texteQuestion;
        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): static
    {
        $this->point = $point;
        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setQuestion($this);
        }
        return $this;
    }

    public function removeOption(Option $option): static
    {
        if ($this->options->removeElement($option)) {
            if ($option->getQuestion() === $this) {
                $option->setQuestion(null);
            }
        }
        return $this;
    }
}
