<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'login' => 'user1',
            'firstname' => 'Sabine',
            'lastname' => 'Mace',
            'email' => 'sabine.mace@aubert.fr',
        ]);

        UserFactory::createOne([
            'login' => 'user2',
            'firstname' => 'Renée',
            'lastname' => 'Michaud',
            'email' => 'renee.michaud@guillot.com',
        ]);

        UserFactory::createOne([
            'login' => 'user3',
            'firstname' => 'Benoît',
            'lastname' => 'Paul',
            'email' => 'benoit.paul@bourgeois.com',
        ]);

        UserFactory::createMany(20);
    }
}
