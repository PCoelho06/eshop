<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setFirstName('Pierre')
              ->setLastName('Coelho')
              ->setEmail('test@test.fr')
              ->setRoles(["ROLE_ADMIN"])
              ->setPassword($this->passwordHasher->hashPassword(
                $admin,
                'password'
                ));
        $manager->persist($admin);

        $faker = Factory::create('fr_FR');
        for ($i = 1; $i < 6; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName())
                 ->setLastName($faker->lastName())
                 ->setEmail($faker->email())
                 ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    'password'
                    ));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
