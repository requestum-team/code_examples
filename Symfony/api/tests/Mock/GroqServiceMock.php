<?php declare(strict_types=1);

namespace App\Tests\Mock;

use App\Service\OpenApi\GroqService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GroqServiceMock extends GroqService
{
    public function __construct()
    {
    }

    public function ask(string $prompt): string
    {
        return "This is a mocked response for prompt: $prompt";
    }
}
