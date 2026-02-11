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
 * IMPORTANT: Ne compte que les participations déjà persistées (ID != null)
 */
public function canBeAccepted(): bool
{
    if (!$this->evenement) {
        return false;
    }

    $capaciteMax = $this->evenement->getCapaciteMax();
    $nbEquipesAcceptees = 0;

    foreach ($this->evenement->getParticipations() as $participation) {
        // CORRECTION: Vérifier que l'ID existe (participation persistée)
        if ($participation->getId() !== null 
            && $participation->getStatut() === StatutParticipation::ACCEPTEE 
            && $participation->getId() !== $this->getId()) {
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
    return $this->canBeAccepted() ? StatutParticipation::ACCEPTEE : StatutParticipation::REJETEE;
}

public function setStatut(StatutParticipation $statut): static
{
    // CORRECTION: Supprimer l'exception ici
    // La vérification capaciteMax doit être faite au niveau Controller
    // Le setStatut() change simplement le statut sans exception
    // Le pattern "auto-validation" veut dire: système suggère le statut via determineStatut()
    // Client/Controller décide s'il force le changement
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
        // CORRECTION LOGIQUE: Vérifier que l'Equipe appartient au même Evenement
        // Une Equipe est liée à UN Evenement spécifique
        // On ne peut pas créer une Participation qui mélange Equipe+Evenement incohérents
        if ($equipe !== null && $this->evenement !== null) {
            if ($equipe->getEvenement() !== $this->evenement) {
                throw new \InvalidArgumentException(
                    "L'équipe '" . $equipe->getNom() . "' n'appartient pas à l'événement '" . $this->evenement->getTitre() . "'"
                );
            }
        }
        $this->equipe = $equipe;
        return $this;
    }



}
