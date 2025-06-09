<?php declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as BaseApiTestCaseAlias;
use App\Tests\Fixtures\TestFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class ApiTestCase extends BaseApiTestCaseAlias
{
    protected function loadFixtures(array $classes): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        foreach ($classes as $class) {
            $fixture = $this->getFixture($class);
            $fixture->load($entityManager);
        }
    }

    private function getFixture(string $class): TestFixtureInterface
    {
        return static::getContainer()->get($class);
    }
}
