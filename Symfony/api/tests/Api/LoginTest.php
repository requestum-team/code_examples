<?php

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Login\LoginUser2FADisabledFixture;
use App\Tests\Fixtures\Login\LoginUser2FAEnabledFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class LoginTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testLoginWith2faDisabledReturnsJwtAndRefreshToken(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LoginUser2FADisabledFixture::class,
        ]);

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $this->assertFalse($data['is2faEnabled']);
        $this->assertNotNull($data['refreshToken']);
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginWith2faEnabledReturnsTempToken(): void
    {
        $client = static::createClient();

        $this->loadFixtures([
            LoginUser2FAEnabledFixture::class,
        ]);

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $this->assertTrue($data['is2faEnabled']);
        $this->assertNull($data['refreshToken']);
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginWithInvalidCredentialsReturnsError(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
