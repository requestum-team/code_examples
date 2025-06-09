<?php declare(strict_types=1);

namespace Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Image\ImageUploadUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @dataProvider validImageProvider
     */
    public function testUserCanUploadImage(string $filename, string $mimeType): void
    {
        // 1. Create test user and authenticate
        $client = static::createClient();
        $client->disableReboot();

        // Save user
        $this->loadFixtures([
            ImageUploadUserFixture::class,
        ]);

        // Simulate login to get token
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertArrayHasKey('token', $data);
        $token = $data['token'];

        // 2. Create a temporary image file
        $fixturePath = __DIR__ . '/../Resources/images/' . $filename;
        $uploadedFile = new UploadedFile($fixturePath, $filename, $mimeType, null, true);

        // 3. Upload image
        $response = $client->request('POST', '/images', [
            'auth_bearer' => $token,
            'body' => [], // force multipart encoding
            'extra' => ['files' => ['file' => $uploadedFile]],
        ]);

        // 4. Assert success
        $this->assertResponseIsSuccessful();
        $responseData = $response->toArray();
        $uploadedFilename = $responseData['filename'] ?? null;
        $this->assertMatchesRegularExpression('/.+\.(jpg|jpeg|png|webp)$/', $uploadedFilename);


        // 5. Remove uploaded file
        if ($uploadedFilename) {
            $uploadedPath = realpath(__DIR__ . '/../../public/uploads/images') . '/' . $uploadedFilename;
            $this->assertFileExists($uploadedPath);
            @unlink($uploadedPath);
            $this->assertFileDoesNotExist($uploadedPath);
        }
    }

    public static function validImageProvider(): array
    {
        return [
            ['user.jpg', 'image/jpeg'],
            ['user.jpeg', 'image/jpeg'],
            ['user.png', 'image/png'],
            ['user.webp', 'image/webp'],
        ];
    }

    /**
     * @dataProvider invalidImageProvider
     */
    public function testUserCannotUploadInvalidImage(string $filename, string $mimeType): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([ImageUploadUserFixture::class]);

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $token = $response->toArray()['token'];

        $filePath = __DIR__ . '/../Resources/text/' . $filename;
        $uploadedFile = new UploadedFile($filePath, $filename, $mimeType, null, true);

        $response = $client->request('POST', '/images', [
            'auth_bearer' => $token,
            'body' => [],
            'extra' => ['files' => ['file' => $uploadedFile]],
        ]);

        $this->assertResponseStatusCodeSame(422);
        $data = $response->toArray(false);

        $this->assertArrayHasKey('detail', $data);
        $this->assertStringContainsString('Please upload a valid image file', $data['detail']);
    }

    public static function invalidImageProvider(): array
    {
        return [
            ['user.txt', 'text/plain'],
        ];
    }

    public function testUserCannotUploadTooLargeImage(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([ImageUploadUserFixture::class]);

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $token = $response->toArray()['token'];

        // Create a fake file > 5MB
        $path = __DIR__ . '/../Resources/images/big_user.jpg';
        $uploadedFile = new UploadedFile($path, 'big_user.jpg', 'image/jpeg', null, true);

        $response = $client->request('POST', '/images', [
            'auth_bearer' => $token,
            'body' => [],
            'extra' => ['files' => ['file' => $uploadedFile]],
        ]);

        $this->assertResponseStatusCodeSame(422);

        $data = $response->toArray(false);
        $this->assertArrayHasKey('detail', $data);
        $this->assertStringContainsString('The file is too large', $data['detail']);
    }
}

