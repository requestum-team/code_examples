<?php declare(strict_types=1);

namespace App\Service\OpenApi;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GroqService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $groqApiKey,
        private string $groqApiModel,
    ) {}

    public function ask(string $prompt): string
    {
        $response = $this->client->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->groqApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->groqApiModel, // or 'llama3-70b-8192'
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 512,
            ],
        ]);

        $data = $response->toArray(false);

        return $data['choices'][0]['message']['content'] ?? '[no response]';
    }
}

