<?php

namespace App\DataFixtures;
use App\Entity\Evenement;
use App\Entity\Equipe;
use App\Entity\Participation;
use App\Entity\Feedback;
use App\Entity\Etudiant;
use App\Enum\TypeEvenement;
use App\Enum\StatutEvenement;
use App\Enum\StatutParticipation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void{
        // Créer des événements de test
        $evenement1 = new Evenement();
        $evenement1->setNom('Hackathon IA 2026');
        $evenement1->setDescription('Compétition de développement d\'applications IA');
        $evenement1->setDateDebut(new \DateTime('2026-03-15 09:00:00'));
        $evenement1->setDateFin(new \DateTime('2026-03-15 18:00:00'));
        $evenement1->setLieu('Campus Esprit');
        $evenement1->setType(TypeEvenement::HACKATHON);
        $evenement1->setStatut(StatutEvenement::PLANIFIE);
        $evenement1->setNbMax(10);
        $evenement1->setWorkflowStatus('planifie');
        $manager->persist($evenement1);

        $evenement2 = new Evenement();
        $evenement2->setNom('Workshop Machine Learning');
        $evenement2->setDescription('Atelier pratique sur le Machine Learning');
        $evenement2->setDateDebut(new \DateTime('2026-04-20 10:00:00'));
        $evenement2->setDateFin(new \DateTime('2026-04-20 16:00:00'));
        $evenement2->setLieu('Salle A101');
        $evenement2->setType(TypeEvenement::WORKSHOP);
        $evenement2->setStatut(StatutEvenement::PLANIFIE);
        $evenement2->setNbMax(8);
        $evenement2->setWorkflowStatus('planifie');
        $manager->persist($evenement2);

        $evenement3 = new Evenement();
        $evenement3->setNom('Conférence Tech 2026');
        $evenement3->setDescription('Conférence sur les nouvelles technologies');
        $evenement3->setDateDebut(new \DateTime('2026-05-10 14:00:00'));
        $evenement3->setDateFin(new \DateTime('2026-05-10 17:00:00'));
        $evenement3->setLieu('Amphithéâtre Central');
        $evenement3->setType(TypeEvenement::CONFERENCE);
        $evenement3->setStatut(StatutEvenement::PLANIFIE);
        $evenement3->setNbMax(15);
        $evenement3->setWorkflowStatus('planifie');
        $manager->persist($evenement3);

        // Note: Pour créer des équipes et participations, vous devez avoir des étudiants
        // Décommentez et adaptez le code ci-dessous si vous avez l'entité Etudiant configurée
        
        /*
        // Exemple de création d'équipes
        $equipe1 = new Equipe();
        $equipe1->setNom('Team Alpha');
        $equipe1->setEvenement($evenement1);
        // Ajouter des étudiants à l'équipe
        $manager->persist($equipe1);

        // Exemple de participation
        $participation1 = new Participation();
        $participation1->setEquipe($equipe1);
        $participation1->setEvenement($evenement1);
        $participation1->setStatut(StatutParticipation::ACCEPTE);
        $participation1->setDateInscription(new \DateTime());
        $manager->persist($participation1);

        // Exemple de feedback
        $feedback1 = new Feedback();
        $feedback1->setEvenement($evenement1);
        // $feedback1->setEtudiant($etudiant1);
        $feedback1->setRatingGlobal(4);
        $feedback1->setRatingOrganisation(5);
        $feedback1->setRatingContenu(4);
        $feedback1->setRatingLieu(4);
        $feedback1->setRatingAnimation(5);
        $feedback1->setSentiment('positif');
        $feedback1->setCommentaire('Excellent événement, très bien organisé!');
        $feedback1->setDateCreation(new \DateTime());
        $manager->persist($feedback1);
        */
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
