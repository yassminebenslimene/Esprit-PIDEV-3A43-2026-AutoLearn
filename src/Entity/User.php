<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Bundle\UserActivityBundle\Entity\UserActivity;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['admin' => Admin::class, 'etudiant' => Etudiant::class])]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé!')]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
<<<<<<< HEAD

#[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Challenge::class)]
private Collection $challenges;

#[ORM\OneToMany(mappedBy: 'user', targetEntity: UserActivity::class, cascade: ['persist', 'remove'])]
private Collection $activities;

=======
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'userId', type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s']+$/u",
        message: 'Le nom ne peut contenir que des lettres, espaces et apostrophes'
    )]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: 'Le nom ne peut pas contenir de chiffres'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s']+$/u",
        message: 'Le prénom ne peut contenir que des lettres, espaces et apostrophes'
    )]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: 'Le prénom ne peut pas contenir de chiffres'
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email "{{ value }}" n\'est pas valide')]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
        message: 'Format d\'email invalide. Exemple : nom.prenom@domaine.com'
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire', groups: ['registration'])]
    #[Assert\Length(
        min: 6,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
        groups: ['registration']
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/",
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&)',
        groups: ['registration']
    )]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Le rôle est obligatoire')]
    #[Assert\Choice(
        choices: ['ADMIN', 'ETUDIANT'],
        message: 'Veuillez choisir un rôle valide'
    )]
    private ?string $role = null;

    #[ORM\Column(name: 'createdAt', type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

<<<<<<< HEAD
    #[ORM\Column(name: 'isSuspended', type: 'boolean', options: ['default' => false])]
    private bool $isSuspended = false;

    #[ORM\Column(name: 'suspendedAt', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $suspendedAt = null;

    #[ORM\Column(name: 'suspensionReason', type: 'string', length: 500, nullable: true)]
    private ?string $suspensionReason = null;

    #[ORM\Column(name: 'suspendedBy', type: 'integer', nullable: true)]
    private ?int $suspendedBy = null;

    #[ORM\Column(name: 'lastLoginAt', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(name: 'lastActivityAt', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastActivityAt = null;

   
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->challenges = new ArrayCollection();
        $this->isSuspended = false;
        $this->lastLoginAt = new \DateTime(); // Initialize with current time
        $this->lastActivityAt = new \DateTime(); // Initialize with current time
        $this->activities = new ArrayCollection();
=======
    /**
     * @var Collection<int, Challenge>
     */
    #[ORM\OneToMany(targetEntity: Challenge::class, mappedBy: 'createdby', orphanRemoval: true)]
    private Collection $Challenges;

    /**
     * @var Collection<int, UserChallenge>
     */
    #[ORM\OneToMany(targetEntity: UserChallenge::class, mappedBy: 'user')]
    private Collection $userChallenges;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'user')]
    private Collection $votes;
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->Challenges = new ArrayCollection();
        $this->userChallenges = new ArrayCollection();
        $this->votes = new ArrayCollection();
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
    }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->role === 'ADMIN') {
            $roles[] = 'ROLE_ADMIN';
        } elseif ($this->role === 'ETUDIANT') {
            $roles[] = 'ROLE_ETUDIANT';
        }
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function sAuthentifier(string $email, string $password): bool
    {
        return ($this->email === $email && password_verify($password, $this->password));
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getClass(): string
    {
        return static::class;
    }
public function isEtudiant(): bool {
    return $this instanceof Etudiant;
}

<<<<<<< HEAD


=======
/**
 * @return Collection<int, Challenge>
 */
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
public function getChallenges(): Collection
{
    return $this->Challenges;
}

public function addChallenge(Challenge $challenge): static
{
    if (!$this->Challenges->contains($challenge)) {
        $this->Challenges->add($challenge);
        $challenge->setCreatedby($this);
    }

    return $this;
}

public function removeChallenge(Challenge $challenge): static
{
<<<<<<< HEAD
    if ($this->challenges->removeElement($challenge)) {
        if ($challenge->getCreatedBy() === $this) {
            $challenge->setCreatedBy(null);
=======
    if ($this->Challenges->removeElement($challenge)) {
        // set the owning side to null (unless already changed)
        if ($challenge->getCreatedby() === $this) {
            $challenge->setCreatedby(null);
>>>>>>> fb4a43f494307a186b8da2e3098a2944d2e0ef9f
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
        $userChallenge->setUser($this);
    }

    return $this;
}

public function removeUserChallenge(UserChallenge $userChallenge): static
{
    if ($this->userChallenges->removeElement($userChallenge)) {
        // set the owning side to null (unless already changed)
        if ($userChallenge->getUser() === $this) {
            $userChallenge->setUser(null);
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

public function addVote(Vote $vote): static
{
    if (!$this->votes->contains($vote)) {
        $this->votes->add($vote);
        $vote->setUser($this);
    }

    return $this;
}

public function removeVote(Vote $vote): static
{
    if ($this->votes->removeElement($vote)) {
        // set the owning side to null (unless already changed)
        if ($vote->getUser() === $this) {
            $vote->setUser(null);
        }
    }

    return $this;
}

public function getIsSuspended(): bool
{
    return $this->isSuspended;
}

public function setIsSuspended(bool $isSuspended): static
{
    $this->isSuspended = $isSuspended;
    return $this;
}

public function getSuspendedAt(): ?\DateTimeInterface
{
    return $this->suspendedAt;
}

public function setSuspendedAt(?\DateTimeInterface $suspendedAt): static
{
    $this->suspendedAt = $suspendedAt;
    return $this;
}

public function getSuspensionReason(): ?string
{
    return $this->suspensionReason;
}

public function setSuspensionReason(?string $suspensionReason): static
{
    $this->suspensionReason = $suspensionReason;
    return $this;
}

public function getSuspendedBy(): ?int
{
    return $this->suspendedBy;
}

public function setSuspendedBy(?int $suspendedBy): static
{
    $this->suspendedBy = $suspendedBy;
    return $this;
}

public function getLastLoginAt(): ?\DateTimeInterface
{
    return $this->lastLoginAt;
}

public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): static
{
    $this->lastLoginAt = $lastLoginAt;
    return $this;
}

public function getLastActivityAt(): ?\DateTimeInterface
{
    return $this->lastActivityAt;
}

public function setLastActivityAt(?\DateTimeInterface $lastActivityAt): static
{
    $this->lastActivityAt = $lastActivityAt;
    return $this;
}

/**
 * @return Collection<int, UserActivity>
 */
public function getActivities(): Collection
{
    return $this->activities;
}

public function addActivity(UserActivity $activity): static
{
    if (!$this->activities->contains($activity)) {
        $this->activities->add($activity);
        $activity->setUser($this);
    }

    return $this;
}

public function removeActivity(UserActivity $activity): static
{
    if ($this->activities->removeElement($activity)) {
        // set the owning side to null (unless already changed)
        if ($activity->getUser() === $this) {
            $activity->setUser(null);
        }
    }

    return $this;
}




}