<?php

namespace App\Entity;

use App\Repository\CommunauteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommunauteRepository::class)]
class Communaute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "userId", nullable: true)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'communaute', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'communaute_members')]
    #[ORM\JoinColumn(name: 'communaute_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'userId', onDelete: 'CASCADE')]
    private Collection $members;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): self { $this->owner = $owner; return $this; }

    public function getPosts(): Collection { return $this->posts; }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCommunaute($this);
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getCommunaute() === $this) {
                $post->setCommunaute(null);
            }
        }
        return $this;
    }

    public function getMembers(): Collection { return $this->members; }

    public function addMember(User $user): self
    {
        if (!$this->members->contains($user) && $user !== $this->owner) {
            $this->members->add($user);
        }
        return $this;
    }

    public function removeMember(User $user): self
    {
        $this->members->removeElement($user);
        return $this;
    }

    public function canPost(?User $user): bool
    {
        if (!$user) return false;

        if ($this->owner && $this->owner->getId() === $user->getId()) {
            return true;
        }

        foreach ($this->members as $member) {
            if ($member->getId() === $user->getId()) {
                return true;
            }
        }

        return false;
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }
}