<?php

namespace App\Entity;

use App\Repository\CommunauteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CommunauteRepository::class)]
class Communaute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // ✅ CORRECTION ICI
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: "owner_id",
        referencedColumnName: "userId",
        nullable: false
    )]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'communaute', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    /** Membres invités (étudiants qui peuvent poster et commenter). L'owner n'est pas dans cette liste. */
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

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

    /** Vérifie si l'utilisateur peut poster/commenter (owner ou membre). */
    public function canPost(?User $user): bool
    {
        if ($user === null) {
            return false;
        }
        $userId = $user->getId();
        if ($this->owner && $this->owner->getId() === $userId) {
            return true;
        }
        foreach ($this->members as $member) {
            if ($member->getId() === $userId) {
                return true;
            }
        }
        return false;
    }
}
