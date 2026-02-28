<?php

namespace App\Entity;

use App\Entity\GestionDeCours\Chapitre;
use App\Repository\ChapterProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChapterProgressRepository::class)]
#[ORM\Table(name: 'chapter_progress')]
#[ORM\UniqueConstraint(name: 'user_chapter_unique', columns: ['user_id', 'chapitre_id'])]
class ChapterProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'userId', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Chapitre::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chapitre $chapitre = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quizScore = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChapitre(): ?Chapitre
    {
        return $this->chapitre;
    }

    public function setChapitre(?Chapitre $chapitre): static
    {
        $this->chapitre = $chapitre;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getQuizScore(): ?int
    {
        return $this->quizScore;
    }

    public function setQuizScore(?int $quizScore): static
    {
        $this->quizScore = $quizScore;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completedAt !== null;
    }
}
