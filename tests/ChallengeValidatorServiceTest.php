<?php

namespace App\Tests;

use App\Entity\Challenge;
use App\Service\ChallengeValidatorService;
use PHPUnit\Framework\TestCase;

class ChallengeValidatorServiceTest extends TestCase
{
    private ChallengeValidatorService $validatorService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->validatorService = new ChallengeValidatorService();
    }
    
    /**
     * Tests pour la règle 1: Titre non vide
     */
    public function testIsTitleValidWithValidTitle(): void
    {
        $challenge = new Challenge();
        $challenge->setTitre('Mon Challenge PHP');
        
        $result = $this->validatorService->isTitleValid($challenge);
        
        $this->assertTrue($result);
    }
    
    public function testIsTitleValidWithEmptyTitle(): void
    {
        $challenge = new Challenge();
        $challenge->setTitre('');
        
        $result = $this->validatorService->isTitleValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsTitleValidWithSpacesOnly(): void
    {
        $challenge = new Challenge();
        $challenge->setTitre('   ');
        
        $result = $this->validatorService->isTitleValid($challenge);
        
        $this->assertFalse($result);
    }
    
    /**
     * Tests pour la règle 2: Description non vide
     */
    public function testIsDescriptionValidWithValidDescription(): void
    {
        $challenge = new Challenge();
        $challenge->setDescription('Ceci est une description valide');
        
        $result = $this->validatorService->isDescriptionValid($challenge);
        
        $this->assertTrue($result);
    }
    
    public function testIsDescriptionValidWithEmptyDescription(): void
    {
        $challenge = new Challenge();
        $challenge->setDescription('');
        
        $result = $this->validatorService->isDescriptionValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsDescriptionValidWithSpacesOnly(): void
    {
        $challenge = new Challenge();
        $challenge->setDescription('   ');
        
        $result = $this->validatorService->isDescriptionValid($challenge);
        
        $this->assertFalse($result);
    }
    
    /**
     * Tests pour la règle 3: Durée > 0
     */
    public function testIsDurationValidWithPositiveDuration(): void
    {
        $challenge = new Challenge();
        $challenge->setDuree(30);
        
        $result = $this->validatorService->isDurationValid($challenge);
        
        $this->assertTrue($result);
    }
    
    public function testIsDurationValidWithZeroDuration(): void
    {
        $challenge = new Challenge();
        $challenge->setDuree(0);
        
        $result = $this->validatorService->isDurationValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsDurationValidWithNegativeDuration(): void
    {
        $challenge = new Challenge();
        $challenge->setDuree(-10);
        
        $result = $this->validatorService->isDurationValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsDurationValidWithNullDuration(): void
    {
        $challenge = new Challenge();
        // duree non définie (null)
        
        $result = $this->validatorService->isDurationValid($challenge);
        
        $this->assertFalse($result);
    }
    
    /**
     * Tests pour la règle 4: Validité des dates
     */
    public function testIsDateRangeValidWithValidDates(): void
    {
        $challenge = new Challenge();
        $challenge->setDateDebut(new \DateTime('2026-01-01'));
        $challenge->setDateFin(new \DateTime('2026-01-10'));
        
        $result = $this->validatorService->isDateRangeValid($challenge);
        
        $this->assertTrue($result);
    }
    
    public function testIsDateRangeValidWithInvalidDates(): void
    {
        $challenge = new Challenge();
        $challenge->setDateDebut(new \DateTime('2026-01-10'));
        $challenge->setDateFin(new \DateTime('2026-01-01'));
        
        $result = $this->validatorService->isDateRangeValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsDateRangeValidWithMissingDates(): void
    {
        $challenge = new Challenge();
        $challenge->setDateDebut(new \DateTime('2026-01-01'));
        // date_fin non définie
        
        $result = $this->validatorService->isDateRangeValid($challenge);
        
        $this->assertFalse($result);
    }
    
    /**
     * Tests pour la règle 5: Niveau dans valeurs autorisées
     */
    public function testIsLevelValidWithValidLevels(): void
    {
        $challenge = new Challenge();
        
        $challenge->setNiveau('Débutant');
        $this->assertTrue($this->validatorService->isLevelValid($challenge));
        
        $challenge->setNiveau('Intermédiaire');
        $this->assertTrue($this->validatorService->isLevelValid($challenge));
        
        $challenge->setNiveau('Avancé');
        $this->assertTrue($this->validatorService->isLevelValid($challenge));
    }
    
    public function testIsLevelValidWithInvalidLevel(): void
    {
        $challenge = new Challenge();
        $challenge->setNiveau('Expert');
        
        $result = $this->validatorService->isLevelValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsLevelValidWithEmptyLevel(): void
    {
        $challenge = new Challenge();
        $challenge->setNiveau('');
        
        $result = $this->validatorService->isLevelValid($challenge);
        
        $this->assertFalse($result);
    }
    /**
     * Tests combinés
     */
    public function testIsValidWithAllRulesValid(): void
    {
        $challenge = $this->createValidChallenge();
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertTrue($result);
    }
    
    public function testIsValidWithInvalidTitle(): void
    {
        $challenge = $this->createValidChallenge();
        $challenge->setTitre(''); // Titre invalide
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsValidWithInvalidDescription(): void
    {
        $challenge = $this->createValidChallenge();
        $challenge->setDescription(''); // Description vide
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsValidWithInvalidDuration(): void
    {
        $challenge = $this->createValidChallenge();
        $challenge->setDuree(0); // Durée invalide
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsValidWithInvalidDates(): void
    {
        $challenge = $this->createValidChallenge();
        $challenge->setDateDebut(new \DateTime('2026-01-10'));
        $challenge->setDateFin(new \DateTime('2026-01-01')); // Dates invalides
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsValidWithInvalidLevel(): void
    {
        $challenge = $this->createValidChallenge();
        $challenge->setNiveau('Expert'); // Niveau non autorisé
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertFalse($result);
    }
    
    public function testIsValidWithMultipleInvalidRules(): void
    {
        $challenge = $this->createValidChallenge();
        $challenge->setTitre(''); // Titre vide
        $challenge->setDuree(-5); // Durée négative
        $challenge->setNiveau('Expert'); // Niveau non autorisé
        
        $result = $this->validatorService->isValid($challenge);
        
        $this->assertFalse($result);
    }
    
    /**
     * Tests pour getErrors()
     */
    public function testGetErrorsWithInvalidChallenge(): void
    {
        $challenge = new Challenge();
        $challenge->setTitre(''); // Titre vide
        $challenge->setDescription(''); // Description vide
        $challenge->setDuree(-10); // Durée négative
        $challenge->setNiveau('Expert'); // Niveau non autorisé
        $challenge->setDateDebut(new \DateTime('2026-01-10'));
        $challenge->setDateFin(new \DateTime('2026-01-01')); // Dates invalides
        
        $errors = $this->validatorService->getErrors($challenge);
        
        $this->assertCount(5, $errors); // 5 erreurs maintenant !
        $this->assertContains("Le titre ne peut pas être vide", $errors);
        $this->assertContains("La description ne peut pas être vide", $errors);
        $this->assertContains("La durée doit être un nombre entier positif", $errors);
        $this->assertContains("Le niveau doit être l'un des suivants : Débutant, Intermédiaire, Avancé", $errors);
        $this->assertContains("La date de fin doit être postérieure à la date de début", $errors);
    }
    
    public function testGetErrorsWithValidChallenge(): void
    {
        $challenge = $this->createValidChallenge();
        
        $errors = $this->validatorService->getErrors($challenge);
        
        $this->assertCount(0, $errors);
    }
    
    /**
     * Méthode utilitaire pour créer un challenge valide
     */
    private function createValidChallenge(): Challenge
    {
        $challenge = new Challenge();
        $challenge->setTitre('Challenge PHP');
        $challenge->setNiveau('Intermédiaire');
        $challenge->setDescription('Un challenge pour tester vos connaissances PHP');
        $challenge->setDuree(30);
        $challenge->setDateDebut(new \DateTime('2026-01-01'));
        $challenge->setDateFin(new \DateTime('2026-01-10'));
        
        return $challenge;
    }
}