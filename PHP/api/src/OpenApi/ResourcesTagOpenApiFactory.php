<?php declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ResourcesTagOpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.openapi.factory')]
        private OpenApiFactoryInterface $decorated,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
        private ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi = $this->applyResourceTags($openApi);
        $openApi = $this->addLoginDocumentation($openApi);

        return $openApi;
    }

    private function applyResourceTags(OpenApi $openApi): OpenApi
    {
        $pathToTag = [];

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $resourceMetadataCollection = $this->resourceMetadataFactory->create($resourceClass);

            foreach ($resourceMetadataCollection as $resourceMetadata) {
                $tag = $resourceMetadata->getExtraProperties()['tag'] ?? null;

                if (!$tag) {
                    continue;
                }

                foreach ($resourceMetadata->getOperations() as $operation) {
                    $pathToTag[$operation->getUriTemplate()] = $tag;
                }
            }
        }

        foreach ($openApi->getPaths()->getPaths() as $path => $pathItem) {
            foreach (['get', 'post', 'put', 'patch', 'delete'] as $method) {
                $getter = 'get' . ucfirst($method);
                $setter = 'with' . ucfirst($method);

                if ($operation = $pathItem->$getter()) {
                    $tag = $pathToTag[$path] ?? null;

                    if ($tag) {
                        $pathItem = $pathItem->$setter($operation->withTags([$tag]));
                        $openApi->getPaths()->addPath($path, $pathItem);
                    }
                }
            }
        }

        return $openApi;
    }

    private function addLoginDocumentation(OpenApi $openApi): OpenApi
    {
        $pathItem = $openApi->getPaths()->getPath('/login');

        $operation = $pathItem->getPost();

        $customOperation = $operation
            ->withSummary('Login to receive JWT token')
            ->withRequestBody(new Model\RequestBody(
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
                                    'refreshToken' => ['type' => 'string'],
                                    "is2faEnabled" => ['type' => 'boolean'],
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
