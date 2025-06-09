<?php declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

final class LoginOverrideOpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        return $this->addLoginDocumentation($openApi);
    }

    private function addLoginDocumentation(OpenApi $openApi): OpenApi
    {
        $pathItem = $openApi->getPaths()->getPath('/login');

        $operation = $pathItem->getPost();

        $customOperation = $operation
            ->withSummary('Login to receive JWT token')
            ->withRequestBody(new RequestBody(
                description: 'Login credentials',
                content: new ArrayObject([
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'email' => ['type' => 'string'],
                                'password' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ]),
            ))
            ->withResponses([
                '200' => [
                    'description' => 'JWT token response',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'token' => ['type' => 'string'],
                                    'refresh_token' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $openApi->getPaths()->addPath('/login', $pathItem->withPost($customOperation));

        return $openApi;
    }
}
