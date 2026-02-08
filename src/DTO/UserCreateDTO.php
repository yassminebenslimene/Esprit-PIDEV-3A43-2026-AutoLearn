<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserCreateDTO
{
    #[Assert\NotBlank(message: "Le nom est requis")]
    #[Assert\Length(min: 2, max: 50)]
    public ?string $nom = null;

    #[Assert\NotBlank(message: "Le prénom est requis")]
    #[Assert\Length(min: 2, max: 50)]
    public ?string $prenom = null;

    #[Assert\NotBlank(message: "L'email est requis")]
    #[Assert\Email]
    public ?string $email = null;

    // ⬇️ PAS NotBlank ici
    #[Assert\Length(min: 6)]
    public ?string $password = null;

    #[Assert\NotBlank(message: "Le rôle est requis")]
    #[Assert\Choice(['ADMIN', 'ETUDIANT'])]
    public ?string $role = null;

    #[Assert\Choice(['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'])]
    public ?string $niveau = null;
}
