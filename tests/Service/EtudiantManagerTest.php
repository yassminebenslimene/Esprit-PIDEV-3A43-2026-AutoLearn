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

    /**
     * Test 1: Validation d'un étudiant valide
     */
    public function testValidEtudiant(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    /**
     * Test 2: Nom obligatoire
     */
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

    /**
     * Test 3: Nom trop court (moins de 2 caractères)
     */
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

    /**
     * Test 4: Nom trop long (plus de 50 caractères)
     */
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

    /**
     * Test 5: Nom avec chiffres (invalide)
     */
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

    /**
     * Test 6: Nom avec caractères spéciaux invalides
     */
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

    /**
     * Test 7: Nom avec apostrophe (valide)
     */
    public function testEtudiantWithNomContainingApostrophe(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom("O'Connor");
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    /**
     * Test 8: Prénom obligatoire
     */
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

    /**
     * Test 9: Prénom trop court
     */
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

    /**
     * Test 10: Prénom avec chiffres
     */
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

    /**
     * Test 11: Email obligatoire
     */
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

    /**
     * Test 12: Email invalide (format incorrect)
     */
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

    /**
     * Test 13: Email sans @
     */
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

    /**
     * Test 14: Niveau obligatoire
     */
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

    /**
     * Test 15: Niveau invalide
     */
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

    /**
     * Test 16: Niveau INTERMEDIAIRE valide
     */
    public function testEtudiantWithNiveauIntermediaire(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('INTERMEDIAIRE');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    /**
     * Test 17: Niveau AVANCE valide
     */
    public function testEtudiantWithNiveauAvance(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Marie');
        $etudiant->setEmail('marie.dupont@example.com');
        $etudiant->setNiveau('AVANCE');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    /**
     * Test 18: Étudiant peut être suspendu
     */
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

    /**
     * Test 19: Étudiant déjà suspendu ne peut pas être suspendu à nouveau
     */
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

    /**
     * Test 20: Validation mot de passe valide
     */
    public function testValidPassword(): void
    {
        $this->assertTrue($this->manager->validatePassword('Password123!'));
    }

    /**
     * Test 21: Mot de passe obligatoire
     */
    public function testPasswordRequired(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe est obligatoire');

        $this->manager->validatePassword('');
    }

    /**
     * Test 22: Mot de passe trop court
     */
    public function testPasswordTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins 6 caractères');

        $this->manager->validatePassword('Pas1!');
    }

    /**
     * Test 23: Mot de passe sans majuscule
     */
    public function testPasswordWithoutUppercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('password123!');
    }

    /**
     * Test 24: Mot de passe sans caractère spécial
     */
    public function testPasswordWithoutSpecialChar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('Password123');
    }

    /**
     * Test 25: Nom avec accents (valide)
     */
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
