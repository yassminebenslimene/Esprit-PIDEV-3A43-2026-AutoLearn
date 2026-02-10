<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserCreateDTO
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 2, max: 50)]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s']+$/u",
        message: 'Le nom ne peut contenir que des lettres, espaces et apostrophes'
    )]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: 'Le nom ne peut pas contenir de chiffres'
    )]
    public ?string $nom = null;

    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(min: 2, max: 50)]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s']+$/u",
        message: 'Le prénom ne peut contenir que des lettres, espaces et apostrophes'
    )]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: 'Le prénom ne peut pas contenir de chiffres'
    )]
    public ?string $prenom = null;

    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email "{{ value }}" n\'est pas valide')]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
        message: 'Format d\'email invalide. Exemple : nom.prenom@domaine.com'
    )]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire', groups: ['registration'])]
    #[Assert\Length(
        min: 8, // Changé de 6 à 8
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
        groups: ['registration']
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/",
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&)',
        groups: ['registration']
    )]
    public ?string $password = null;

    #[Assert\NotBlank(message: 'Le rôle est obligatoire')]
    #[Assert\Choice(['ADMIN', 'ETUDIANT'])]
    public ?string $role = null;

    #[Assert\Choice(['DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'], groups: ['niveau_validation'])]
    #[Assert\NotBlank(message: 'Le niveau est obligatoire pour un étudiant', groups: ['niveau_validation'])]
    public ?string $niveau = null;
}