<?php

namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AccountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; ++$i) {
            $account = new Account();
            $account->setMomoNumber($faker->phoneNumber())
                ->setType($faker->randomElement(['MTN', 'GASA', 'USER']));
                $manager->persist($account);
        }
        $manager->flush();
    }
}
