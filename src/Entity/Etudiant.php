<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Etudiant extends User
{
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le niveau est requis")]
    #[Assert\Choice(
        choices: ['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'],
        message: "Choisissez un niveau valide"
    )]
    private ?string $niveau = null;

    public function __construct()
    {
        parent::__construct();
        $this->setRole('ETUDIANT');
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;
        return $this;
    }
}