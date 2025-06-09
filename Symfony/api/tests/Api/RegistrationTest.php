<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Tests\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class RegistrationTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testRegisterUser(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/register', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'SecurePass123',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
        ]);

        // ✅ Assert HTTP 201 Created
        $this->assertResponseStatusCodeSame(201);

        // ✅ Assert JSON structure (optional)
        $this->assertJsonContains([
            'email' => 'user@example.com',
        ]);

        // ✅ Optionally check that the user was saved to the DB
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
            'email' => 'user@example.com',
        ]);

        $this->assertNotNull($user);
        $this->assertSame('user@example.com', $user->getEmail());
        $this->assertSame('John', $user->getFirstName());
        $this->assertSame('Doe', $user->getLastName());
    }

    public function testRegisterWithExistingEmail(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // Create a user first
        $client->request('POST', '/register', [
            'json' => [
                'email' => 'duplicate@example.com',
                'password' => 'SecurePass123',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        // Check user in database
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
            'email' => 'duplicate@example.com',
        ]);

        $this->assertNotNull($user, 'User should be saved after first request');

        // Try again with the same email
        $client->request('POST', '/register', [
            'json' => [
                'email' => 'duplicate@example.com',
                'password' => 'AnotherPass456',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'description' => 'User exist',
        ]);
    }
}
