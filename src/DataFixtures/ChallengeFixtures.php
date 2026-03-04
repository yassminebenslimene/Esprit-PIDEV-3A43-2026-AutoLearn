<?php

namespace App\DataFixtures;

use App\Entity\Challenge;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChallengeFixtures extends Fixture  // ← Retirez "implements DependentFixtureInterface"
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer un utilisateur existant (admin ou autre)
        $user = $manager->getRepository(User::class)->findOneBy([]);
        
        if (!$user) {
            // Si aucun utilisateur n'existe, on en crée un
            $user = new \App\Entity\Admin();
            $user->setEmail('admin@test.com');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setNom('Admin');
            $user->setPrenom('Test');
            $user->setRole('ADMIN');
            $manager->persist($user);
            $manager->flush(); // Flush pour avoir l'ID
        }

        // Créer un challenge de test avec ID 1
        $challenge = new Challenge();
        $challenge->setTitre('Challenge de test');
        $challenge->setDescription('Ceci est un challenge de test pour les fixtures');
        $challenge->setDateDebut(new \DateTime('now'));
        $challenge->setDateFin(new \DateTime('+7 days'));
        $challenge->setNiveau('Intermédiaire');
        $challenge->setDuree(30);
        $challenge->setCreatedBy($user);
        
        $manager->persist($challenge);
        
        // Créer un deuxième challenge
        $challenge2 = new Challenge();
        $challenge2->setTitre('Challenge PHP');
        $challenge2->setDescription('Challenge sur les bases de PHP');
        $challenge2->setDateDebut(new \DateTime('now'));
        $challenge2->setDateFin(new \DateTime('+5 days'));
        $challenge2->setNiveau('Débutant');
        $challenge2->setDuree(20);
        $challenge2->setCreatedBy($user);
        
        $manager->persist($challenge2);
        
        $manager->flush();
    }
}