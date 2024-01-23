<?php

namespace App\DataFixtures;

use App\Factory\BookmarkFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookmarkFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // BookmarkFactory::createMany(20);

        $file = __DIR__.'/data/bookmarks.json';
        $data = json_decode(file_get_contents($file), true);
        foreach ($data as $tab) {
            BookmarkFactory::createOne($tab);
        }
    }
}
