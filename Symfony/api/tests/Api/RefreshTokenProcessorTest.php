<?php declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Token\RefreshTokenUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class RefreshTokenProcessorTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testRefreshTokenReturnsNewJwt(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            RefreshTokenUserFixture::class,
        ]);

        // Log in and get initial tokens
        $response = $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('refreshToken', $data);

        $refreshToken = $data['refreshToken'];

        // Use the refresh token
        $refreshResponse = $client->request('POST', '/token/refresh', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $refreshData = $refreshResponse->toArray();

        $this->assertArrayHasKey('token', $refreshData);
        $this->assertNotEmpty($refreshData['token']);
    }
}
