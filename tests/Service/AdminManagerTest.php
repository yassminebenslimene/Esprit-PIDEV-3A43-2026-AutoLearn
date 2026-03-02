<?php

namespace App\Tests\Service;

use App\Entity\Admin;
use App\Service\AdminManager;
use PHPUnit\Framework\TestCase;

class AdminManagerTest extends TestCase
{
    private AdminManager $manager;

    protected function setUp(): void
    {
        $this->manager = new AdminManager();
    }

    /**
     * Test 1: Validation d'un admin valide
     */
    public function testValidAdmin(): void
    {
        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean.dupont@autolearn.com');

        $this->assertTrue($this->manager->validate($admin));
    }

    /**
     * Test 2: Nom obligatoire
     */
    public function testAdminWithoutNom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom est obligatoire');

        $admin = new Admin();
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 3: Nom trop court (moins de 2 caractères)
     */
    public function testAdminWithNomTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom doit contenir au moins 2 caractères');

        $admin = new Admin();
        $admin->setNom('D');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 4: Nom trop long (plus de 50 caractères)
     */
    public function testAdminWithNomTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom ne peut pas dépasser 50 caractères');

        $admin = new Admin();
        $admin->setNom(str_repeat('A', 51));
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 5: Nom avec chiffres (invalide)
     */
    public function testAdminWithNomContainingNumbers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom ne peut pas contenir de chiffres');

        $admin = new Admin();
        $admin->setNom('Dupont123');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 6: Nom avec caractères spéciaux invalides
     */
    public function testAdminWithNomContainingSpecialChars(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom ne peut contenir que des lettres, espaces et apostrophes');

        $admin = new Admin();
        $admin->setNom('Dupont@#$');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 7: Prénom obligatoire
     */
    public function testAdminWithoutPrenom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom est obligatoire');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setEmail('dupont@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 8: Prénom trop court
     */
    public function testAdminWithPrenomTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom doit contenir au moins 2 caractères');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('J');
        $admin->setEmail('j.dupont@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 9: Prénom avec chiffres
     */
    public function testAdminWithPrenomContainingNumbers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom ne peut pas contenir de chiffres');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean123');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

    /**
     * Test 10: Email obligatoire
     */
    public function testAdminWithoutEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'email est obligatoire');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');

        $this->manager->validate($admin);
    }

    /**
     * Test 11: Email invalide (format incorrect)
     */
    public function testAdminWithInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'email n\'est pas valide');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');
        $admin->setEmail('email_invalide');

        $this->manager->validate($admin);
    }

    /**
     * Test 12: Email sans domaine
     */
    public function testAdminWithEmailWithoutDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@');

        $this->manager->validate($admin);
    }

    /**
     * Test 13: Admin ne peut pas être suspendu
     */
    public function testAdminCannotBeSuspended(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Un administrateur ne peut pas être suspendu');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->canBeSuspended($admin);
    }

    /**
     * Test 14: Validation mot de passe valide
     */
    public function testValidPassword(): void
    {
        $this->assertTrue($this->manager->validatePassword('Password123!'));
    }

    /**
     * Test 15: Mot de passe obligatoire
     */
    public function testPasswordRequired(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe est obligatoire');

        $this->manager->validatePassword('');
    }

    /**
     * Test 16: Mot de passe trop court
     */
    public function testPasswordTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins 6 caractères');

        $this->manager->validatePassword('Pas1!');
    }

    /**
     * Test 17: Mot de passe sans majuscule
     */
    public function testPasswordWithoutUppercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('password123!');
    }

    /**
     * Test 18: Mot de passe sans minuscule
     */
    public function testPasswordWithoutLowercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('PASSWORD123!');
    }

    /**
     * Test 19: Mot de passe sans chiffre
     */
    public function testPasswordWithoutNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('Password!');
    }

    /**
     * Test 20: Mot de passe sans caractère spécial
     */
    public function testPasswordWithoutSpecialChar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('Password123');
    }
}
