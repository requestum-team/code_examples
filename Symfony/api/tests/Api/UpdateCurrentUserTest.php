<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\User\SingleUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UpdateCurrentUserTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testUserCanUpdateOwnProfile(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            SingleUserFixture::class,
        ]);

        // Login
        $loginResponse = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $token = $loginResponse->toArray()['token'];

        // Patch user profile
        $client->request('PATCH', '/users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'firstName' => 'Updated',
                'lastName' => 'Name',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'firstName' => 'Updated',
            'lastName' => 'Name',
        ]);
    }

    public function testUserCanUpdateOwnProfileOnlyFirstName(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            SingleUserFixture::class,
        ]);

        // Login
        $loginResponse = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $token = $loginResponse->toArray()['token'];

        // Patch user profile
        $client->request('PATCH', '/users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'firstName' => 'Updated',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'firstName' => 'Updated',
            'lastName' => 'Doe',
        ]);
    }

    public function testUserCanUpdateOwnProfileOnlyLastName(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            SingleUserFixture::class,
        ]);

        // Login
        $loginResponse = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $token = $loginResponse->toArray()['token'];

        // Patch user profile
        $client->request('PATCH', '/users/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'lastName' => 'Updated',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'firstName' => 'John',
            'lastName' => 'Updated',
        ]);
    }
}
