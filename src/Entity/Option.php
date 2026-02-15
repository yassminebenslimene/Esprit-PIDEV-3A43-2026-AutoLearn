<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le texte de l'option est obligatoire.")]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: "L'option doit contenir au moins {{ limit }} caractère.",
        maxMessage: "L'option ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $texteOption = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Vous devez préciser si l'option est correcte ou non.")]
    #[Assert\Type(
        type: 'bool',
        message: "La valeur doit être un booléen (vrai ou faux)."
    )]
    private ?bool $estCorrecte = null;

    #[ORM\ManyToOne(inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'option doit être associée à une question.")]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexteOption(): ?string
    {
        return $this->texteOption;
    }

    public function setTexteOption(string $texteOption): static
    {
        $this->texteOption = $texteOption;
        return $this;
    }

    public function isEstCorrecte(): ?bool
    {
        return $this->estCorrecte;
    }

    public function setEstCorrecte(bool $estCorrecte): static
    {
        $this->estCorrecte = $estCorrecte;
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;
        return $this;
    }
}
