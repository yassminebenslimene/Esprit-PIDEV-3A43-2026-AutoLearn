<?php

namespace App\DataFixtures;

use App\Entity\GestionDeCours\Cours;
use App\Entity\GestionDeCours\Chapitre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WebDevelopmentFixtures extends Fixture
{
    use WebDevelopmentContent;
    public function load(ObjectManager $manager): void
    {
        // Créer le cours Web Development
        $cours = new Cours();
        $cours->setTitre('Web Development - HTML CSS JavaScript');
        $cours->setDescription('Complete web development course covering HTML, CSS, and JavaScript. Learn to build modern, responsive websites from scratch.');
        $cours->setMatiere('Developpement Web');
        $cours->setNiveau('Debutant');
        $cours->setDuree(60);
        $cours->setCreatedAt(new \DateTimeImmutable());

        // Créer les chapitres
        $this->createChapter1($cours);
        $this->createChapter2($cours);
        $this->createChapter3($cours);
        $this->createChapter4($cours);
        $this->createChapter5($cours);
        $this->createChapter6($cours);
        $this->createChapter7($cours);
        $this->createChapter8($cours);
        $this->createChapter9($cours);
        $this->createChapter10($cours);

        $manager->persist($cours);
        $manager->flush();
    }

    private function createChapter1(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('Introduction to HTML');
        $chapitre->setOrdre(1);
        $chapitre->setContenu($this->getChapter1Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter2(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('HTML Structure and Semantics');
        $chapitre->setOrdre(2);
        $chapitre->setContenu($this->getChapter2Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter3(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('Introduction to CSS');
        $chapitre->setOrdre(3);
        $chapitre->setContenu($this->getChapter3Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter4(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('CSS Layout and Positioning');
        $chapitre->setOrdre(4);
        $chapitre->setContenu($this->getChapter4Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter5(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('Responsive Web Design');
        $chapitre->setOrdre(5);
        $chapitre->setContenu($this->getChapter5Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter6(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('Introduction to JavaScript');
        $chapitre->setOrdre(6);
        $chapitre->setContenu($this->getChapter6Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter7(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('JavaScript DOM Manipulation');
        $chapitre->setOrdre(7);
        $chapitre->setContenu($this->getChapter7Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter8(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('JavaScript Events and Interactivity');
        $chapitre->setOrdre(8);
        $chapitre->setContenu($this->getChapter8Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter9(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('JavaScript Async and Fetch API');
        $chapitre->setOrdre(9);
        $chapitre->setContenu($this->getChapter9Content());
        $cours->addChapitre($chapitre);
    }

    private function createChapter10(Cours $cours): void
    {
        $chapitre = new Chapitre();
        $chapitre->setTitre('Building a Complete Web Project');
        $chapitre->setOrdre(10);
        $chapitre->setContenu($this->getChapter10Content());
        $cours->addChapitre($chapitre);
    }

    private function getChapter1Content(): string
    {
        return 'Introduction to HTML - Basic structure and elements';
    }

    private function getChapter2Content(): string
    {
        return 'HTML Structure and Semantics - Semantic HTML5 elements';
    }

    private function getChapter3Content(): string
    {
        return 'Introduction to CSS - Styling basics and selectors';
    }

    private function getChapter4Content(): string
    {
        return 'CSS Layout and Positioning - Flexbox and Grid';
    }

    private function getChapter5Content(): string
    {
        return 'Responsive Web Design - Media queries and mobile-first approach';
    }

    private function getChapter6Content(): string
    {
        return 'Introduction to JavaScript - Variables, data types, and functions';
    }

    private function getChapter7Content(): string
    {
        return 'JavaScript DOM Manipulation - Selecting and modifying elements';
    }

    private function getChapter8Content(): string
    {
        return 'JavaScript Events and Interactivity - Event listeners and handlers';
    }

    private function getChapter9Content(): string
    {
        return 'JavaScript Async and Fetch API - Promises and async/await';
    }

    private function getChapter10Content(): string
    {
        return 'Building a Complete Web Project - Putting it all together';
    }

}