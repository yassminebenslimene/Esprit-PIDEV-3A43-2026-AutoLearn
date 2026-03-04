<?php

namespace App\DataFixtures;

use App\Entity\GestionDeCours\Cours;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoursFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        

        $manager->flush();
    }
}