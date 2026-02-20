<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['admin' => Admin::class, 'etudiant' => Etudiant::class])]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé!')]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{

#[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Challenge::class)]
private Collection $challenges;


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'userId', type: 'integer')]
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

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isSuspended = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $suspendedAt = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $suspensionReason = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $suspendedBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastLoginAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->challenges = new ArrayCollection();
        $this->isSuspended = false;
        $this->lastLoginAt = new \DateTime(); // Initialize with current time
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



public function getChallenges(): Collection
{
    return $this->challenges;
}

public function addChallenge(Challenge $challenge): static
{
    if (!$this->challenges->contains($challenge)) {
        $this->challenges->add($challenge);
        $challenge->setCreatedBy($this);
    }
    return $this;
}

public function removeChallenge(Challenge $challenge): static
{
    if ($this->challenges->removeElement($challenge)) {
        if ($challenge->getCreatedBy() === $this) {
            $challenge->setCreatedBy(null);
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

}