<?php

namespace App\Tests\Service;

use App\Entity\Etudiant;
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
     * Test 1: Validation d'un utilisateur valide
     */
    public function testValidUser(): void
    {
        $user = new Etudiant();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('jean.dupont@example.com');
        $user->setRole('ETUDIANT');

        $this->assertTrue($this->manager->validate($user));
    }

    /**
     * Test 2: Validation échoue si le nom est vide
     */
    public function testUserWithoutNom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom est obligatoire');

        $user = new Etudiant();
        $user->setPrenom('Jean');
        $user->setEmail('jean@example.com');
        $user->setRole('ETUDIANT');

        $this->manager->validate($user);
    }

    /**
     * Test 3: Validation échoue si le prénom est vide
     */
    public function testUserWithoutPrenom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom est obligatoire');

        $user = new Etudiant();
        $user->setNom('Dupont');
        $user->setEmail('dupont@example.com');
        $user->setRole('ETUDIANT');

        $this->manager->validate($user);
    }

    /**
     * Test 4: Validation échoue si l'email est invalide
     */
    public function testUserWithInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email invalide');

        $user = new Etudiant();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('email_invalide');
        $user->setRole('ETUDIANT');

        $this->manager->validate($user);
    }

    /**
     * Test 5: Validation échoue si le rôle est invalide
     */
    public function testUserWithInvalidRole(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le rôle doit être ETUDIANT, ENSEIGNANT ou ADMIN');

        $user = new Etudiant();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('jean.dupont@example.com');
        $user->setRole('ROLE_INVALIDE');

        $this->manager->validate($user);
    }

    /**
     * Test 6: Un utilisateur non suspendu peut être suspendu
     */
    public function testUserCanBeSuspended(): void
    {
        $user = new Etudiant();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('jean.dupont@example.com');
        $user->setRole('ETUDIANT');
        $user->setIsSuspended(false);

        $this->assertTrue($this->manager->canBeSuspended($user));
    }

    /**
     * Test 7: Un utilisateur déjà suspendu ne peut pas être suspendu à nouveau
     */
    public function testAlreadySuspendedUserCannotBeSuspended(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'utilisateur est déjà suspendu');

        $user = new Etudiant();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('jean.dupont@example.com');
        $user->setRole('ETUDIANT');
        $user->setIsSuspended(true);

        $this->manager->canBeSuspended($user);
    }

    /**
     * Test 8: Un administrateur ne peut pas être suspendu
     */
    public function testAdminCannotBeSuspended(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Un administrateur ne peut pas être suspendu');

        $user = new Etudiant();
        $user->setNom('Admin');
        $user->setPrenom('Super');
        $user->setEmail('admin@example.com');
        $user->setRole('ADMIN');
        $user->setIsSuspended(false);

        $this->manager->canBeSuspended($user);
    }

    /**
     * Test 9: Validation d'un enseignant valide
     */
    public function testValidEnseignant(): void
    {
        $user = new Etudiant();
        $user->setNom('Martin');
        $user->setPrenom('Sophie');
        $user->setEmail('sophie.martin@example.com');
        $user->setRole('ENSEIGNANT');

        $this->assertTrue($this->manager->validate($user));
    }

    /**
     * Test 10: Validation d'un admin valide
     */
    public function testValidAdmin(): void
    {
        $user = new Etudiant();
        $user->setNom('Admin');
        $user->setPrenom('Super');
        $user->setEmail('admin@autolearn.com');
        $user->setRole('ADMIN');

        $this->assertTrue($this->manager->validate($user));
    }
}
