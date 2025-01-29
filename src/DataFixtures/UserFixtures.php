<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; ++$i) {
            $user = new User();
            $user->setFirstname($faker->userName)
            ->setLastname($faker->userName)     
                ->setMatricule(mt_rand(1, 19))
                ->setType($faker->randomElement(['ROLE_USER', 'ROLE_USER']))        // Exemple de type statique
                ->setRoles([$faker->randomElement(['ROLE_USER', 'ROLE_ADMIN'])])    // Exemple de rôle statique
                ->setBirthDay(new \DateTime()) // Date de naissance actuelle
                ->setIsVerified($faker->boolean())
                ->setEmail($faker->email) // Exemple d'email statique
                ->setDatanumber('1234567890') 
                ->setPassword('richard123'); 
            
            $manager->persist($user);
            $manager->flush();
            
        }
    }
}
