<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        for ($i = 1; $i < 5; $i++) {
            
            $categorie = $this->getReference('categorie_' . $faker->numberBetween(1, 10));
            $product = new Product();
            $product->setCategory($categorie)
                    ->setName($faker->word(3, true))
                    ->setDescription($faker->paragraph(2))
                    ->setPrice($faker->numberBetween(10, 200))
                    ->setSubtitle($faker->word(3, true))
                    ->setIllustration($faker->image('C:\laragon\www\boutique\public\uploads\images', 360, 360, 'animals', false, true, 'cats', true));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
