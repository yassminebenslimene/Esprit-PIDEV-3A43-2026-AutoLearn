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

    
    public function testValidAdmin(): void
    {
        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean.dupont@autolearn.com');

        $this->assertTrue($this->manager->validate($admin));
    }

 
    public function testAdminWithoutNom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom est obligatoire');

        $admin = new Admin();
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@autolearn.com');

        $this->manager->validate($admin);
    }

   
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

 
    public function testAdminWithoutPrenom(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le prénom est obligatoire');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setEmail('dupont@autolearn.com');

        $this->manager->validate($admin);
    }

 
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


    public function testAdminWithoutEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'email est obligatoire');

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');

        $this->manager->validate($admin);
    }

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

    public function testAdminWithEmailWithoutDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $admin = new Admin();
        $admin->setNom('Dupont');
        $admin->setPrenom('Jean');
        $admin->setEmail('jean@');

        $this->manager->validate($admin);
    }

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


    public function testPasswordWithoutLowercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('PASSWORD123!');
    }


    public function testPasswordWithoutNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('Password!');
    }


    public function testPasswordWithoutSpecialChar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial');

        $this->manager->validatePassword('Password123');
    }
}
