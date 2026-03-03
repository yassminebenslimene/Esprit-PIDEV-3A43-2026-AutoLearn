<?php

namespace App\Tests\Service;

use App\Entity\Etudiant;
use App\Service\EtudiantManager;
use PHPUnit\Framework\TestCase;

class EtudiantManagerTest extends TestCase
{
    private EtudiantManager $manager;

    protected function setUp(): void
    {
        $this->manager = new EtudiantManager();
    }

    public function testValidEtudiant(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    public function testEtudiantWithoutNom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom est obligatoire');

        $etudiant = new Etudiant();
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithNomTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom doit contenir au moins 2 caractères');

        $etudiant = new Etudiant();
        $etudiant->setNom('D');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithNomTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom ne peut pas dépasser 50 caractères');

        $etudiant = new Etudiant();
        $etudiant->setNom(str_repeat('A', 51));
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithNomContainingNumbers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom ne peut pas contenir de chiffres');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont123');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithNomContainingSpecialChars(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom ne peut contenir que des lettres, espaces et apostrophes');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont@#$');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithNomContainingApostrophe(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom("O'Connor");
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    public function testEtudiantWithoutPrenom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom est obligatoire');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setEmail('dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithPrenomTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom doit contenir au moins 2 caractères');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('M');
        $etudiant->setEmail('m.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithPrenomContainingNumbers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom ne peut pas contenir de chiffres');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie123');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithoutEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'email est obligatoire');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'email n\'est pas valide');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('email_invalide');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithEmailWithoutAt(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marieexample.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithoutNiveau(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le niveau est obligatoire pour un étudiant');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithInvalidNiveau(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le niveau doit être DEBUTANT, INTERMEDIAIRE ou AVANCE');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('EXPERT');

        $this->manager->validate($etudiant);
    }

    public function testEtudiantWithNiveauIntermediaire(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('INTERMEDIAIRE');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    public function testEtudiantWithNiveauAvance(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('AVANCE');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    public function testEtudiantCanBeSuspended(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');
        $etudiant->setIsSuspended(false);

        $this->assertTrue($this->manager->canBeSuspended($etudiant));
    }

    public function testAlreadySuspendedEtudiantCannotBeSuspended(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'étudiant est déjà suspendu');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');
        $etudiant->setIsSuspended(true);

        $this->manager->canBeSuspended($etudiant);
    }

    public function testValidPassword(): void
    {
        $this->assertTrue($this->manager->validatePassword('Password123!'));
    }

    public function testPasswordRequired(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe est obligatoire');

        $this->manager->validatePassword('');
    }

    public function testPasswordTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins 6 caractères');

        $this->manager->validatePassword('Pas1!');
    }

    public function testPasswordWithoutUppercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('password123!');
    }

    public function testPasswordWithoutSpecialChar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('Password123');
    }

    public function testEtudiantWithNomContainingAccents(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Müller');
        $etudiant->setPrenom('François');
        $etudiant->setEmail('francois@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->assertTrue($this->manager->validate($etudiant));
    }
}
