<?php declare(strict_types=1);

namespace App\Tests\Fixtures;

use Doctrine\Persistence\ObjectManager;

interface TestFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void;
}
