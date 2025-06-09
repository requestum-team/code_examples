<?php declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CleanupRefreshTokensCommand;
use App\Entity\RefreshToken;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Token\RefreshTokenClearCommandFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CleanupRefreshTokensCommandTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testExpiredTokensAreRemoved(): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->loadFixtures([
            RefreshTokenClearCommandFixture::class,
        ]);

        // Assert that expired token exist
        $tokens = $entityManager->getRepository(RefreshToken::class)->findAll();
        $this->assertCount(1, $tokens);

        // Run the command
        $application = new Application();
        $command = static::getContainer()->get(CleanupRefreshTokensCommand::class);
        $application->add($command);

        $tester = new CommandTester($application->find('app:cleanup-refresh-tokens'));
        $tester->execute([]);

        // Assert the expired token is gone
        $tokens = $entityManager->getRepository(RefreshToken::class)->findAll();
        $this->assertCount(0, $tokens);
    }
}
