<?php declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\TextGeneration\TextGenerationUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class TextGenerationTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testBasicPromptGeneratesResponse(): void
    {
        // Prepare
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            TextGenerationUserFixture::class,
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

        // Ask text generation
        $response = $client->request('POST', '/ask', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'prompt' => 'Explain what Symfony Messenger is',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();

        $this->assertStringContainsString(
            'application/ld+json',
            $response->getHeaders(false)['content-type'][0]
        );

        $this->assertArrayHasKey('reply', $data);
        $this->assertNotEmpty($data['reply']);
        $this->assertStringContainsStringIgnoringCase('symfony', $data['reply']);
    }

    public function testEmptyPromptFailsValidation(): void
    {
        // Prepare
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            TextGenerationUserFixture::class,
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

        // Ask text generation
        $client->request('POST', '/ask', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'prompt' => '',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(422);

        $data = $client->getResponse()->toArray(false);
        $this->assertStringContainsString('prompt', json_encode($data));
    }
}

