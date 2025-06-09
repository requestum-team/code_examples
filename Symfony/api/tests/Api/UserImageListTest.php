<?php declare(strict_types=1);

namespace Api;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\Image\ImageUploadSeparateUserFixture;
use App\Tests\Fixtures\Image\ImageUploadUserFixture;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserImageListTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testUserSeesTheirOwnImages(): void
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

        $filePath = __DIR__ . '/../Resources/images/user.jpg';
        $uploadedFile = new UploadedFile($filePath, 'test.jpg', 'image/jpeg', null, true);

        $uploadResponse = $client->request('POST', '/images', [
            'auth_bearer' => $token,
            'body' => [],
            'extra' => ['files' => ['file' => $uploadedFile]],
        ]);
        $this->assertResponseIsSuccessful();
        $imageData = $uploadResponse->toArray();

        $response = $client->request('GET', '/users/me/images', [
            'auth_bearer' => $token,
        ]);
        $this->assertResponseIsSuccessful();

        $images = $response->toArray()['member'];
        $this->assertCount(1, $images);
        $this->assertSame($imageData['filename'], $images[0]['filename']);

        @unlink(__DIR__ . '/../../public/uploads/images/' . $imageData['filename']);
    }

    public function testAnotherUserSeesNoImages(): void
    {
        $client = static::createClient();
        $client->disableReboot();

        $this->loadFixtures([
            ImageUploadUserFixture::class,
            ImageUploadSeparateUserFixture::class,
        ]);

        $loginResponse = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@example.com',
                'password' => 'user123',
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $uploaderToken = $loginResponse->toArray()['token'];

        $filePath = __DIR__ . '/../Resources/images/user.jpg';
        $uploadedFile = new UploadedFile($filePath, 'test.jpg', 'image/jpeg', null, true);
        $uploadResponse = $client->request('POST', '/images', [
            'auth_bearer' => $uploaderToken,
            'body' => [],
            'extra' => ['files' => ['file' => $uploadedFile]],
        ]);
        $this->assertResponseIsSuccessful();
        $filename = $uploadResponse->toArray()['filename'];

        $secondLogin = $client->request('POST', '/login', [
            'json' => [
                'email' => 'separate_user@example.com',
                'password' => 'user123',
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $secondToken = $secondLogin->toArray()['token'];

        $response = $client->request('GET', '/users/me/images', [
            'auth_bearer' => $secondToken,
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertSame([], $response->toArray()['member']);

        @unlink(__DIR__ . '/../../public/uploads/images/' . $filename);
    }
}
