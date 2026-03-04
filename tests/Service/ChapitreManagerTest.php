<?php

namespace App\Tests\Service;

use App\Entity\GestionDeCours\Chapitre;
use App\Entity\GestionDeCours\Cours;
use App\Service\ChapitreManager;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChapitreManagerTest extends TestCase
{
    private ChapitreManager $manager;

    protected function setUp(): void
    {
        $this->manager = new ChapitreManager();
    }

    /**
     * Test: Un chapitre valide doit passer la validation
     */
    public function testValidChapitre(): void
    {
        // Arrange: Créer un chapitre valide
        $chapitre = new Chapitre();
        $chapitre->setTitre('Introduction aux bases de données');
        $chapitre->setContenu('Les bases de données sont des systèmes qui permettent de stocker, de gérer et de...');
        $chapitre->setOrdre(1);

        // Act & Assert: La validation doit réussir
        $this->assertTrue($this->manager->validate($chapitre));
    }

    /**
     * Test: Un chapitre sans titre doit échouer
     */
    public function testChapitreWithoutTitre(): void
    {
        // Arrange: Créer un chapitre sans titre
        $chapitre = new Chapitre();
        $chapitre->setContenu('Contenu du chapitre');
        $chapitre->setOrdre(1);

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le titre du chapitre est obligatoire');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }

    /**
     * Test: Un chapitre avec un titre vide doit échouer
     */
    public function testChapitreWithEmptyTitre(): void
    {
        // Arrange: Créer un chapitre avec titre vide
        $chapitre = new Chapitre();
        $chapitre->setTitre('');
        $chapitre->setContenu('Contenu du chapitre');
        $chapitre->setOrdre(1);

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le titre du chapitre est obligatoire');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }

    /**
     * Test: Un chapitre sans contenu doit échouer
     */
    public function testChapitreWithoutContenu(): void
    {
        // Arrange: Créer un chapitre sans contenu
        $chapitre = new Chapitre();
        $chapitre->setTitre('Titre du chapitre');
        $chapitre->setOrdre(1);

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le contenu du chapitre ne peut pas être vide');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }

    /**
     * Test: Un chapitre avec un contenu vide doit échouer
     */
    public function testChapitreWithEmptyContenu(): void
    {
        // Arrange: Créer un chapitre avec contenu vide
        $chapitre = new Chapitre();
        $chapitre->setTitre('Titre du chapitre');
        $chapitre->setContenu('');
        $chapitre->setOrdre(1);

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le contenu du chapitre ne peut pas être vide');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }

    /**
     * Test: Un chapitre avec un ordre négatif doit échouer
     */
    public function testChapitreWithNegativeOrdre(): void
    {
        // Arrange: Créer un chapitre avec ordre négatif
        $chapitre = new Chapitre();
        $chapitre->setTitre('Titre du chapitre');
        $chapitre->setContenu('Contenu du chapitre');
        $chapitre->setOrdre(-1);

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }

    /**
     * Test: Un chapitre avec un ordre zéro doit échouer
     */
    public function testChapitreWithZeroOrdre(): void
    {
        // Arrange: Créer un chapitre avec ordre zéro
        $chapitre = new Chapitre();
        $chapitre->setTitre('Titre du chapitre');
        $chapitre->setContenu('Contenu du chapitre');
        $chapitre->setOrdre(0);

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }

    /**
     * Test: Un chapitre sans ordre doit échouer
     */
    public function testChapitreWithoutOrdre(): void
    {
        // Arrange: Créer un chapitre sans ordre
        $chapitre = new Chapitre();
        $chapitre->setTitre('Titre du chapitre');
        $chapitre->setContenu('Contenu du chapitre');
        // Ne pas définir l'ordre

        // Assert: Une exception doit être levée
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('L\'ordre du chapitre doit être un nombre positif');

        // Act: Tenter de valider
        $this->manager->validate($chapitre);
    }
}
