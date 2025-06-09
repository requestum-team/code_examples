<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\TwoFactor\TwoFactorVerifyUser2FADisabledFixture;
use App\Tests\Fixtures\TwoFactor\TwoFactorVerifyUser2FAEnabledFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use OTPHP\TOTP;

class TwoFactorVerifyTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function test2FAAuthenticationFlow(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            TwoFactorVerifyUser2FAEnabledFixture::class,
        ]);

        // 1. Login with email & password
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $this->assertTrue($data['is2faEnabled']);
        $this->assertArrayHasKey('token', $data);

        $tempToken = $data['token'];

        // 2. Extract secret from database
        /** @var User $user */
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([
            'email' => 'user@example.com',
        ]);

        $this->assertNotNull($user);
        $totp = TOTP::create($user->getTotpSecret());
        $code = $totp->now();

        // 3. Send TOTP code to /2fa/verify
        $verifyResponse = $client->request('POST', '/2fa/verify', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'token' => $tempToken,
                'totpCode' => $code,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);

        $tokens = $verifyResponse->toArray();

        $this->assertArrayHasKey('token', $tokens);
        $this->assertArrayHasKey('refreshToken', $tokens);
    }

    public function test2FAVerifyWithInvalidCode(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        // Load a fixture with 2FA enabled user
        $this->loadFixtures([
            TwoFactorVerifyUser2FAEnabledFixture::class,
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
        $tempToken = $data['token'];

        // Step 2: Try verifying with invalid TOTP code
        $verifyResponse = $client->request('POST', '/2fa/verify', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'token' => $tempToken,
                'totpCode' => '000000', // <- intentionally invalid
            ],
        ]);

        // Step 3: Assert response is 401 Unauthorized
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'description' => 'Authentication failed',
        ]);
    }

    public function testVerifyFailsWhen2faIsDisabled(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            TwoFactorVerifyUser2FADisabledFixture::class,
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

        $client->request('POST', '/2fa/verify', [
            'json' => [
                'token' => $token,
                'totpCode' => '000000',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'description' => 'Invalid or expired token',
        ]);
    }
}
