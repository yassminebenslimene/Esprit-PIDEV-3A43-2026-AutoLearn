<?php

namespace App\DataFixtures;

use App\Entity\GestionDeCours\Cours;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoursFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Cours 1: Python pour débutants
        $cours1 = new Cours();
        $cours1->setTitre('Python pour Débutants');
        $cours1->setDescription('Apprenez les bases de la programmation Python. Ce cours couvre les variables, les boucles, les fonctions et les structures de données fondamentales. Parfait pour ceux qui débutent en programmation.');
        $cours1->setMatiere('Programmation');
        $cours1->setNiveau('Débutant');
        $cours1->setDuree(40);
        $cours1->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours1);

        // Cours 2: JavaScript Moderne
        $cours2 = new Cours();
        $cours2->setTitre('JavaScript Moderne (ES6+)');
        $cours2->setDescription('Maîtrisez JavaScript moderne avec ES6+. Découvrez les arrow functions, les promesses, async/await, les modules et bien plus. Créez des applications web interactives et dynamiques.');
        $cours2->setMatiere('Développement Web');
        $cours2->setNiveau('Intermédiaire');
        $cours2->setDuree(50);
        $cours2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours2);

        // Cours 3: Développement Web avec React
        $cours3 = new Cours();
        $cours3->setTitre('Développement Web avec React');
        $cours3->setDescription('Créez des applications web modernes avec React. Apprenez les composants, les hooks, le state management, et les meilleures pratiques pour construire des interfaces utilisateur performantes.');
        $cours3->setMatiere('Framework Frontend');
        $cours3->setNiveau('Intermédiaire');
        $cours3->setDuree(60);
        $cours3->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours3);

        // Cours 4: Bases de données SQL
        $cours4 = new Cours();
        $cours4->setTitre('Bases de Données SQL');
        $cours4->setDescription('Apprenez à concevoir et gérer des bases de données relationnelles. Maîtrisez SQL, les requêtes complexes, les jointures, les index et l\'optimisation des performances.');
        $cours4->setMatiere('Base de données');
        $cours4->setNiveau('Débutant');
        $cours4->setDuree(35);
        $cours4->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours4);

        // Cours 5: PHP et Symfony
        $cours5 = new Cours();
        $cours5->setTitre('PHP et Symfony Framework');
        $cours5->setDescription('Développez des applications web robustes avec PHP et Symfony. Découvrez l\'architecture MVC, Doctrine ORM, Twig, les formulaires et la sécurité dans Symfony.');
        $cours5->setMatiere('Backend Development');
        $cours5->setNiveau('Intermédiaire');
        $cours5->setDuree(70);
        $cours5->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours5);

        // Cours 6: Git et GitHub
        $cours6 = new Cours();
        $cours6->setTitre('Git et GitHub pour Développeurs');
        $cours6->setDescription('Maîtrisez le contrôle de version avec Git et GitHub. Apprenez les branches, les merges, les pull requests, et les workflows collaboratifs pour travailler en équipe efficacement.');
        $cours6->setMatiere('Outils de développement');
        $cours6->setNiveau('Débutant');
        $cours6->setDuree(25);
        $cours6->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours6);

        // Cours 7: Java Orienté Objet
        $cours7 = new Cours();
        $cours7->setTitre('Java et Programmation Orientée Objet');
        $cours7->setDescription('Apprenez Java et les concepts de la programmation orientée objet. Classes, héritage, polymorphisme, interfaces et design patterns pour créer des applications robustes.');
        $cours7->setMatiere('Programmation');
        $cours7->setNiveau('Intermédiaire');
        $cours7->setDuree(55);
        $cours7->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours7);

        // Cours 8: HTML et CSS
        $cours8 = new Cours();
        $cours8->setTitre('HTML5 et CSS3 Fondamentaux');
        $cours8->setDescription('Créez des pages web modernes avec HTML5 et CSS3. Apprenez la structure HTML, le styling CSS, Flexbox, Grid, et les animations pour des sites web attractifs et responsives.');
        $cours8->setMatiere('Développement Web');
        $cours8->setNiveau('Débutant');
        $cours8->setDuree(30);
        $cours8->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours8);

        // Cours 9: Node.js et Express
        $cours9 = new Cours();
        $cours9->setTitre('Node.js et Express Backend');
        $cours9->setDescription('Développez des APIs REST avec Node.js et Express. Apprenez à créer des serveurs, gérer les routes, connecter des bases de données et sécuriser vos applications backend.');
        $cours9->setMatiere('Backend Development');
        $cours9->setNiveau('Intermédiaire');
        $cours9->setDuree(45);
        $cours9->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours9);

        // Cours 10: Introduction à l'IA
        $cours10 = new Cours();
        $cours10->setTitre('Introduction à l\'Intelligence Artificielle');
        $cours10->setDescription('Découvrez les fondamentaux de l\'IA et du Machine Learning. Apprenez les algorithmes de base, le traitement des données et créez vos premiers modèles d\'apprentissage automatique avec Python.');
        $cours10->setMatiere('Intelligence Artificielle');
        $cours10->setNiveau('Avancé');
        $cours10->setDuree(80);
        $cours10->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($cours10);

        $manager->flush();
    }
}
