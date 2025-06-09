<?php declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\TwoFactor\TwoFactorEnableUser2FADisabledFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class TwoFactorEnableTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testEnable2FAAuthenticated(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // Load a fixture with 2FA enabled user
        $this->loadFixtures([
            TwoFactorEnableUser2FADisabledFixture::class,
        ]);

        // Step 1: Log in with email/password to get temp JWT with `2fa_required`
        $loginResponse = $client->request('POST', '/login', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $loginResponse->toArray();
        $token = $data['token'];

        $response = $client->request('POST', '/2fa/enable', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = $response->toArray();

        $this->assertArrayHasKey('qrUrl', $data);
        $this->assertArrayHasKey('secret', $data);
        $this->assertNotEmpty($data['qrUrl']);
        $this->assertNotEmpty($data['secret']);
    }

    public function testEnable2FAUnauthorized(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/2fa/enable', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);

        $data = $response->toArray(false);
        $this->assertArrayHasKey('message', $data);
        $this->assertSame('JWT Token not found', $data['message']);
    }
}
