<?php declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Password\ResetPasswordUserFixture;
use App\Tests\Fixtures\Password\ResetPasswordUserWithUsedTokenFixture;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testResetPasswordSuccessfully(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        // Load user and manually create a token
        $this->loadFixtures([
            ResetPasswordUserFixture::class,
        ]);

        // Send reset request
        $client->request('POST', '/reset-password', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'token' => 'test-reset-token',
                'newPassword' => 'newSecurePassword123',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        // Verify token marked used
        $token = static::getContainer()
            ->get('doctrine')
            ->getRepository(PasswordResetToken::class)
            ->findOneByToken('test-reset-token');
        $this->assertTrue($token->isUsed());

        // Verify password was updated
        $user = static::getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneByEmail('user@example.com');
        $this->assertTrue($hasher->isPasswordValid($user, 'newSecurePassword123'));
    }

    public function testResetPasswordFailsWithInvalidToken(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $client->request('POST', '/reset-password', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'token' => 'non-existent-token',
                'newPassword' => 'irrelevant',
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Invalid or expired token.',
        ]);
    }

    public function testResetPasswordFailsWhenTokenAlreadyUsed(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            ResetPasswordUserWithUsedTokenFixture::class,
        ]);

        // Attempt to reuse the token
        $client->request('POST', '/reset-password', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'token' => 'used-token',
                'newPassword' => 'anotherPassword123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Invalid or expired token.',
        ]);
    }

}
