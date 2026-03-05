<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    private ?int $valeur = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'votes')]  // AJOUTÉ inversedBy
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "userId", nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Challenge::class, inversedBy: "votes")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Challenge $challenge = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdvoteAt = null;

    public function __construct()
    {
        $this->createdvoteAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(?int $valeur): static
    {
        $this->valeur = $valeur;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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

    public function getCreatedvoteAt(): ?\DateTimeImmutable
    {
        return $this->createdvoteAt;
    }

    public function setCreatedvoteAt(?\DateTimeImmutable $createdvoteAt): static
    {
        $this->createdvoteAt = $createdvoteAt;
        return $this;
    }
}