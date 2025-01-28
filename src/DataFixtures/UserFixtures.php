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
                ->setMatricule($faker->numberBetween(1, 7))
                ->setType($faker->randomElement(['LIMITE', 'ILIMITE']))
                ->setRoles([$faker->randomElement(['ROLE_USER', 'ROLE_ADMIN'])])
                ->setBirthDay(new \DateTime())
                ->setIsVerified($faker->boolean(0))
                ->setEmail($faker->email)
                ->setPassword($faker->password('richard'))
                ->setDatanumber($faker->phoneNumber);

            $manager->persist($user);
            $manager->flush();
        }
    }
}
