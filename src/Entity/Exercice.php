<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "La question ne peut pas être vide.")]
    private ?string $question = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "La réponse ne peut pas être vide.")]
    private ?string $reponse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de points ne peut pas être vide.")]
    #[Assert\Positive(message: "Le nombre de points doit être un entier positif.")]
    private ?int $points = null;

    #[ORM\ManyToOne(inversedBy: 'exercices')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Challenge $challenge = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

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
}
