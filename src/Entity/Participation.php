<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParticipationRepository;
use App\Enum\StatutParticipation;
use App\Entity\Equipe;
use App\Entity\Evenement;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Equipe $equipe;

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: "participations")]
    #[ORM\JoinColumn(nullable: false)]
    private Evenement $evenement;

    #[ORM\Column(length:50, enumType: StatutParticipation::class)]
    private StatutParticipation $statut = StatutParticipation::EN_ATTENTE;

    /**
     * Validation automatique de la participation
     */
    public function validateParticipation(): void
    {
        $evenement = $this->getEvenement();
        $equipe = $this->getEquipe();

        // Vérification nbMax - ne compter que les participations ACCEPTÉES (sauf celle-ci)
        $acceptedCount = 0;
        foreach ($evenement->getParticipations() as $p) {
            // Ne pas compter la participation actuelle
            if ($p->getId() === $this->getId()) {
                continue;
            }
            if ($p->getStatut() === StatutParticipation::ACCEPTE) {
                $acceptedCount++;
            }
        }
        
        if ($acceptedCount >= $evenement->getNbMax()) {
            $this->setStatut(StatutParticipation::REFUSE);
            return;
        }

        // Vérification doublon étudiants - ne vérifier que les autres participations
        foreach ($evenement->getParticipations() as $p) {
            // Ne pas vérifier contre soi-même
            if ($p->getId() === $this->getId()) {
                continue;
            }
            
            // Vérifier uniquement les participations acceptées
            if ($p->getStatut() !== StatutParticipation::ACCEPTE) {
                continue;
            }
            
            foreach ($p->getEquipe()->getEtudiants() as $etudiant) {
                foreach ($equipe->getEtudiants() as $membre) {
                    if ($etudiant->getId() === $membre->getId()) {
                        $this->setStatut(StatutParticipation::REFUSE);
                        return;
                    }
                }
            }
        }

        $this->setStatut(StatutParticipation::ACCEPTE);
    }

    // ===== Getters / Setters =====
    public function getId(): ?int { return $this->id; }

    public function getEquipe(): Equipe { return $this->equipe; }
    public function setEquipe(Equipe $equipe): self { $this->equipe = $equipe; return $this; }

    public function getEvenement(): Evenement { return $this->evenement; }
    public function setEvenement(Evenement $evenement): self { $this->evenement = $evenement; return $this; }

    public function getStatut(): StatutParticipation { return $this->statut; }
    public function setStatut(StatutParticipation $statut): self { $this->statut = $statut; return $this; }
}
