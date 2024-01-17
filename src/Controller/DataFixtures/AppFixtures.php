<?php

namespace App\Controller\DataFixtures;

use App\Factory\PostFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $objectManager): void
    {
        $startTime = microtime(true);

        PostFactory::createMany(5);
        UserFactory::createMany(5);

        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;
    }
}