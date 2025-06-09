<?php declare(strict_types=1);

// tests/Api/ForgotPasswordTest.php
namespace App\Tests\Api;

use App\Entity\PasswordResetToken;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Password\ForgotPasswordUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mailer\EventListener\MessageLoggerListener;
use Symfony\Component\Mailer\MailerInterface;

class ForgotPasswordTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use MailerAssertionsTrait;

    public function testForgotPasswordCreatesTokenAndSendsEmail(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // Load user from fixture
        $this->loadFixtures([
            ForgotPasswordUserFixture::class, // Adjust if your fixture class is named differently
        ]);

        $client->request('POST', '/forgot-password', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => ['email' => 'user@example.com'],
        ]);

        $this->assertResponseIsSuccessful();

        $tokens = static::getContainer()
            ->get('doctrine')
            ->getRepository(PasswordResetToken::class)
            ->findAll();

        $this->assertCount(1, $tokens);

        // Assert email
        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();
        $this->assertEmailHeaderSame($email, 'to', 'user@example.com');
        $this->assertEmailTextBodyContains($email, 'Use this token to reset your password');
    }
}

