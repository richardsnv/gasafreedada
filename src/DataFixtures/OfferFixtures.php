<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OfferFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; ++$i) {
            $offer = new Offer();
            $offer->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 years', 'now') ))
            ->setDeadLine($faker->numberBetween(1, 30))
            ->setQuantity($faker->numberBetween(5, 100))
            ->setType($faker->randomElement(['ILLIMITE', 'LIMITE'])) 
            ->setPrice($faker->numberBetween(150, 300));
            $manager->persist($offer);
            $manager->flush();
        }
    }
}
