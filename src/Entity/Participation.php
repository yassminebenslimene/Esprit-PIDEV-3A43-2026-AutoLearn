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

public function setStatut(StatutParticipation $statut): static
{
    if ($statut === StatutParticipation::ACCEPTEE) {

        $capaciteMax = $this->evenement->getCapaciteMax();
        $nbEquipesAcceptees = 0;

        foreach ($this->evenement->getParticipations() as $participation) {
            if ($participation->getStatut() === StatutParticipation::ACCEPTEE) {
                $nbEquipesAcceptees++;
            }
        }

        if ($nbEquipesAcceptees >= $capaciteMax) {
            throw new \LogicException("Le nombre maximal d'équipes acceptées est atteint.");
        }
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
