<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class VrpFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        //Création de 30 Commerciaux
        for ($i = 1; $i <= 30; $i++) {
            $user = new User();
            $user->setName($faker->name())
                ->setEmail($faker->email())
                ->setRoles(['ROLE_USER'])
                ->setUsername($faker->userName())
                ->setPassword($faker->password())
                ->setCreatedAt($faker->dateTimeBetween('-5 years'));

            $manager->persist($user);

            //Création de 50 à 100 notes par user
            for ($j = 1; $j <= mt_rand(50, 100); $j++) {
                $note = new Note();

                $now = new \DateTime();
                $interval = $now->diff($user->getCreatedAt());
                $days = $interval->days;
                $minimum = '-' . $days . 'days';

                $note->setDate($faker->dateTimeBetween($minimum))
                    ->setMontant($faker->randomFloat(2, 1, 200))
                    ->setType($faker->word())
                    ->setScan('http://placehold.it/350x150')
                    ->setStatut('En cours')
                    ->setAdress($faker->streetAddress())
                    ->setPostcode(mt_rand(01000, 94000))
                    ->setCity($faker->city())
                    ->setCountry($faker->country())
                    ->setLatitude($faker->latitude())
                    ->setLongitude($faker->longitude())
                    ->setCreatedAt(new \DateTime())
                    ->setDescription($faker->sentence())
                    ->setUser($user);

                $manager->persist($note);
            }
        }

        $manager->flush();

        $manager->flush();
    }
}
