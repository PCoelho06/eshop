<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // création des catégories
        for ($i = 1; $i < 11; $i++) {
        $categorie = new Category();
        $categorie->setName('CAT' . $i);
        $manager->persist($categorie);
        $this->addReference('categorie_' . $i, $categorie);
        }

        $manager->flush();
    }
}
