<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\EvenementManager;
use App\Entity\Evenement;
use App\Enum\TypeEvenement;

/**
 * Tests unitaires pour le service EvenementManager
 * 
 * Règles métier testées:
 * 1. Le titre doit contenir au moins 5 caractères
 * 2. La description doit contenir au moins 10 caractères
 * 3. Le lieu ne doit pas être vide
 * 4. La date de fin doit être postérieure à la date de début
 * 5. Le nombre maximum d'équipes doit être entre 1 et 100
 * 6. La date de début doit être dans le futur
 */
class EvenementManagerTest extends TestCase
{
    /**
     * Test 1: Événement valide avec toutes les règles métier respectées
     */
    public function testValidEvenement(): void
    {
        $evenement = new Evenement();
        $evenement->setTitre('Hackathon 2026');
        $evenement->setLieu('Tunis');
        $evenement->setDescription('Un super hackathon pour les étudiants');
        $evenement->setType(TypeEvenement::HACKATHON);
        $evenement->setDateDebut(new \DateTime('+1 day'));
        $evenement->setDateFin(new \DateTime('+2 days'));
        $evenement->setNbMax(10);

        $manager = new EvenementManager();
        $this->assertTrue($manager->validate($evenement));
    }

    /**
     * Test 2: Titre trop court (moins de 5 caractères)
     */
    public function testTitreTropCourt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le titre doit contenir au moins 5 caractères');

        $evenement = new Evenement();
        $evenement->setTitre('Hack'); // ERREUR: seulement 4 caractères
        $evenement->setLieu('Tunis');
        $evenement->setDescription('Un super hackathon pour les étudiants');
        $evenement->setType(TypeEvenement::HACKATHON);
        $evenement->setDateDebut(new \DateTime('+1 day'));
        $evenement->setDateFin(new \DateTime('+2 days'));
        $evenement->setNbMax(10);

        $manager = new EvenementManager();
        $manager->validate($evenement);
    }

    /**
     * Test 3: Description trop courte (moins de 10 caractères)
     */
    public function testDescriptionTropCourte(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La description doit contenir au moins 10 caractères');

        $evenement = new Evenement();
        $evenement->setTitre('Hackathon 2026');
        $evenement->setLieu('Tunis');
        $evenement->setDescription('Court'); // ERREUR: seulement 5 caractères
        $evenement->setType(TypeEvenement::HACKATHON);
        $evenement->setDateDebut(new \DateTime('+1 day'));
        $evenement->setDateFin(new \DateTime('+2 days'));
        $evenement->setNbMax(10);

        $manager = new EvenementManager();
        $manager->validate($evenement);
    }

    /**
     * Test 4: Date de fin avant la date de début
     */
    public function testDateFinBeforeDateDebut(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La date de fin doit être postérieure à la date de début');

        $evenement = new Evenement();
        $evenement->setTitre('Hackathon 2026');
        $evenement->setLieu('Tunis');
        $evenement->setDescription('Un super hackathon pour les étudiants');
        $evenement->setType(TypeEvenement::HACKATHON);
        $evenement->setDateDebut(new \DateTime('+2 days'));
        $evenement->setDateFin(new \DateTime('+1 day')); // ERREUR: avant dateDebut
        $evenement->setNbMax(10);

        $manager = new EvenementManager();
        $manager->validate($evenement);
    }

    /**
     * Test 5: Nombre maximum d'équipes négatif
     */
    public function testNbMaxNegatif(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nombre maximum d\'équipes doit être entre 1 et 100');

        $evenement = new Evenement();
        $evenement->setTitre('Hackathon 2026');
        $evenement->setLieu('Tunis');
        $evenement->setDescription('Un super hackathon pour les étudiants');
        $evenement->setType(TypeEvenement::HACKATHON);
        $evenement->setDateDebut(new \DateTime('+1 day'));
        $evenement->setDateFin(new \DateTime('+2 days'));
        $evenement->setNbMax(-5); // ERREUR: négatif

        $manager = new EvenementManager();
        $manager->validate($evenement);
    }

    /**
     * Test 6: Date de début dans le passé
     */
    public function testDateDebutDansLePasse(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La date de début doit être dans le futur');

        $evenement = new Evenement();
        $evenement->setTitre('Hackathon 2026');
        $evenement->setLieu('Tunis');
        $evenement->setDescription('Un super hackathon pour les étudiants');
        $evenement->setType(TypeEvenement::HACKATHON);
        $evenement->setDateDebut(new \DateTime('-1 day')); // ERREUR: dans le passé
        $evenement->setDateFin(new \DateTime('+1 day'));
        $evenement->setNbMax(10);

        $manager = new EvenementManager();
        $manager->validate($evenement);
    }
}
