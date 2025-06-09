<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\User\SingleUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class GetCurrentUserTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCurrentUserProfile(): void
    {
        // Prepare
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            SingleUserFixture::class,
        ]);

        // Login
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $token = $response->toArray()['token'];

        // Get Current User Profile
        $client->request('GET', '/users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]);
    }
}
