<?php

namespace App\Tests\Service;

use App\Entity\Quiz;
use App\Service\QuizManager;
use PHPUnit\Framework\TestCase;

// Classe de test pour le service QuizManager
class QuizManagerTest extends TestCase
{
    private QuizManager $quizManager;

    // Méthode exécutée avant chaque test pour initialiser le service
    protected function setUp(): void
    {
        $this->quizManager = new QuizManager();
    }

    // Test : Vérifier qu'un étudiant réussit le quiz avec un bon score
    public function testIsQuizPassedWithGoodScore(): void
    {
        // Créer un quiz avec un seuil de réussite de 50%
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('actif');
        $quiz->setSeuilReussite(50);

        // Tester avec un score de 80%
        $result = $this->quizManager->isQuizPassed($quiz, 80.0);

        // Vérifier que le résultat est TRUE (80% >= 50%)
        $this->assertTrue($result, "Le quiz devrait être réussi avec 80% (seuil: 50%)");
    }

    // Test : Vérifier qu'un étudiant échoue le quiz avec un mauvais score
    public function testIsQuizPassedWithBadScore(): void
    {
        // Créer un quiz avec un seuil de réussite de 50%
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('actif');
        $quiz->setSeuilReussite(50);

        // Tester avec un score de 30%
        $result = $this->quizManager->isQuizPassed($quiz, 30.0);

        // Vérifier que le résultat est FALSE (30% < 50%)
        $this->assertFalse($result, "Le quiz devrait être échoué avec 30% (seuil: 50%)");
    }

    // Test : Vérifier qu'un quiz avec l'état 'actif' est bien actif
    public function testIsQuizActive(): void
    {
        // Créer un quiz avec l'état 'actif'
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('actif');

        // Vérifier que le quiz est actif
        $result = $this->quizManager->isQuizActive($quiz);

        // Le résultat doit être TRUE
        $this->assertTrue($result, "Le quiz devrait être actif");
    }

    // Test : Vérifier qu'un quiz en brouillon n'est pas actif
    public function testIsQuizInactive(): void
    {
        // Créer un quiz avec l'état 'brouillon'
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('brouillon');

        // Vérifier que le quiz n'est pas actif
        $result = $this->quizManager->isQuizActive($quiz);

        // Le résultat doit être FALSE
        $this->assertFalse($result, "Le quiz ne devrait pas être actif");
    }

    // Test : Vérifier qu'un score invalide (> 100) génère une erreur
    public function testIsQuizPassedWithInvalidScore(): void
    {
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('actif');
        $quiz->setSeuilReussite(50);

        // On s'attend à une exception pour un score > 100
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le score doit être entre 0 et 100");

        $this->quizManager->isQuizPassed($quiz, 150.0);
    }

    // Test : Vérifier qu'un score négatif génère une erreur
    public function testIsQuizPassedWithNegativeScore(): void
    {
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('actif');
        $quiz->setSeuilReussite(50);

        // On s'attend à une exception pour un score négatif
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le score doit être entre 0 et 100");

        $this->quizManager->isQuizPassed($quiz, -10.0);
    }

    // Test : Vérifier qu'un état invalide génère une erreur
    public function testIsQuizActiveWithInvalidState(): void
    {
        $quiz = new Quiz();
        $quiz->setTitre('Test Quiz');
        $quiz->setDescription('Description test');
        $quiz->setEtat('invalide'); // État non valide

        // On s'attend à une exception pour un état invalide
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("État invalide");

        $this->quizManager->isQuizActive($quiz);
    }
}
