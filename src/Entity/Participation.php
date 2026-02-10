<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Evenement;
use App\Entity\Equipe;
use App\Enum\StatutParticipation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ORM\Table(name: 'participation', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'unique_participation', columns: ['evenement_id', 'equipe_id'])
])]


class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaireAdmin = null;

    
   


    #[ORM\Column(enumType: StatutParticipation::class)]
    #[Assert\NotBlank]
    private ?StatutParticipation $statut = null;

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: "participations")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Evenement $evenement = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe = null;

   public function __construct()
   {
    $this->dateInscription = new \DateTimeImmutable();
    $this->statut = StatutParticipation::EN_ATTENTE;
        
   }

    

    // === GETTERS & SETTERS ===

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeImmutable $dateInscription): static
    {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    public function getCommentaireAdmin(): ?string
    {
        return $this->commentaireAdmin;
    }

    public function setCommentaireAdmin(?string $commentaireAdmin): static
    {
        $this->commentaireAdmin = $commentaireAdmin;
        return $this;
    }

    public function getStatut(): ?StatutParticipation
{
    return $this->statut;
}

/**
 * Vérifie si cette participation peut être acceptée (sans exception)
 * Retourne true si la capaciteMax n'est pas atteinte
 */
public function canBeAccepted(): bool
{
    if (!$this->evenement) {
        return false;
    }

    $capaciteMax = $this->evenement->getCapaciteMax();
    $nbEquipesAcceptees = 0;

    foreach ($this->evenement->getParticipations() as $participation) {
        if ($participation->getStatut() === StatutParticipation::ACCEPTEE && $participation->getId() !== $this->getId()) {
            $nbEquipesAcceptees++;
        }
    }

    return $nbEquipesAcceptees < $capaciteMax;
}

/**
 * Auto-détermine le statut en fonction de la capaciteMax
 * Retourne ACCEPTEE si possible, REFUSEE sinon
 */
public function determineStatut(): StatutParticipation
{
    return $this->canBeAccepted() ? StatutParticipation::ACCEPTEE : StatutParticipation::REFUSEE;
}

public function setStatut(StatutParticipation $statut): static
{
    // Si on essaie de passer à ACCEPTEE sans vérification, c'est un problème
    if ($statut === StatutParticipation::ACCEPTEE && !$this->canBeAccepted()) {
        throw new \LogicException("Le nombre maximal d'équipes acceptées est atteint pour cet événement.");
    }

    $this->statut = $statut;
    return $this;
}


    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        $this->equipe = $equipe;
        return $this;
    }



}
