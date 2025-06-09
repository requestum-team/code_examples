<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\User\UserWithImagesFixture;
use App\Tests\Fixtures\User\UserWithoutImagesFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class SetAvatarTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testUserCanSetOwnAvatar(): void
    {
        // Create client
        $client = static::createClient();
        $client->disableReboot();

        // Load data
        $this->loadFixtures([
            UserWithImagesFixture::class,
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

        // Get Images
        $response = $client->request('GET', '/users/me/images', [
            'auth_bearer' => $token,
        ]);

        $images = $response->toArray()['member'];
        $imageId = $images[0]['id'];
        $this->assertNotNull($imageId);

        // Change avatar
        $response = $client->request('PUT', "/users/me/avatar", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'imageId' => $imageId,
            ],
        ]);

        // Assert
        $responseData = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame($images[0]['url'], $responseData['avatarUrl']);
    }

    public function testUserCanNotSetOtherUserAvatar(): void
    {
        // Create client
        $client = static::createClient();
        $client->disableReboot();

        // Load data
        $this->loadFixtures([
            UserWithImagesFixture::class,
            UserWithoutImagesFixture::class,
        ]);

        // Login as right user
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $token = $response->toArray()['token'];

        // Get Images
        $response = $client->request('GET', '/users/me/images', [
            'auth_bearer' => $token,
        ]);

        $images = $response->toArray()['member'];
        $imageId = $images[0]['id'];
        $this->assertNotNull($imageId);

        // Login as another user

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'another_user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $token = $response->toArray()['token'];

        // Change avatar
        $client->request('PUT', "/users/me/avatar", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'imageId' => $imageId,
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(400);
    }
}
