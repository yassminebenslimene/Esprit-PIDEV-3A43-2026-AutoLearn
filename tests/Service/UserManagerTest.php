<?php

namespace App\Tests\Service;

use App\Entity\Etudiant;
use App\Entity\Admin;
use App\Service\UserManager;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    private UserManager $manager;

    protected function setUp(): void
    {
        $this->manager = new UserManager();
    }

    /**
     * Test 1: Validation d'un étudiant valide
     */
    public function testValidEtudiant(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('jean.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->assertTrue($this->manager->validate($etudiant));
    }

    /**
     * Test 2: Validation échoue si le nom est vide
     */
    public function testEtudiantWithoutNom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom est obligatoire');

        $etudiant = new Etudiant();
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('jean@example.com');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    /**
     * Test 3: Validation échoue si le prénom est vide
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
     * Test 4: Validation échoue si l'email est invalide
     */
    public function testEtudiantWithInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email invalide');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('email_invalide');
        $etudiant->setNiveau('DEBUTANT');

        $this->manager->validate($etudiant);
    }

    /**
     * Test 5: Validation échoue si le niveau est vide pour un étudiant
     */
    public function testEtudiantWithoutNiveau(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le niveau est obligatoire pour un étudiant');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('jean.dupont@example.com');

        $this->manager->validate($etudiant);
    }

    /**
     * Test 6: Validation échoue si le niveau est invalide pour un étudiant
     */
    public function testEtudiantWithInvalidNiveau(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le niveau doit être DEBUTANT, INTERMEDIAIRE ou AVANCE');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('jean.dupont@example.com');
        $etudiant->setNiveau('NIVEAU_INVALIDE');

        $this->manager->validate($etudiant);
    }

    /**
     * Test 7: Un étudiant non suspendu peut être suspendu
     */
    public function testEtudiantCanBeSuspended(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('jean.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');
        $etudiant->setIsSuspended(false);

        $this->assertTrue($this->manager->canBeSuspended($etudiant));
    }

    /**
     * Test 8: Un étudiant déjà suspendu ne peut pas être suspendu à nouveau
     */
    public function testAlreadySuspendedEtudiantCannotBeSuspended(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'utilisateur est déjà suspendu');

        $etudiant = new Etudiant();
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Jean');
        $etudiant->setEmail('jean.dupont@example.com');
        $etudiant->setNiveau('DEBUTANT');
        $etudiant->setIsSuspended(true);

        $this->manager->canBeSuspended($etudiant);
    }

    /**
     * Test 9: Un administrateur ne peut pas être suspendu
     */
    public function testAdminCannotBeSuspended(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Un administrateur ne peut pas être suspendu');

        $admin = new Admin();
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setEmail('admin@example.com');
        $admin->setIsSuspended(false);

        $this->manager->canBeSuspended($admin);
    }

    /**
     * Test 10: Validation d'un admin valide
     */
    public function testValidAdmin(): void
    {
        $admin = new Admin();
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setEmail('admin@autolearn.com');

        $this->assertTrue($this->manager->validate($admin));
    }
}
