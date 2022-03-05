<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final class PostCreateTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldCreatePost(): void
    {
        $client = self::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/posts/create');

        $client->submitForm('Créer', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('post_read', ['id' => 126]);

        /** @var PostRepository $postRepository */
        $postRepository = $client->getContainer()->get(PostRepository::class);

        /** @var Post|null $post */
        $post = $postRepository->find(126);

        self::assertNotNull($post);
        self::assertEquals('Title', $post->getTitle());
        self::assertEquals('Content', $post->getContent());
        self::assertEquals(1, $post->getCategory()->getId());
        self::assertCount(2, $post->getTags());
    }

    /**
     * @test
     */
    public function shouldRedirectToLogin(): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/posts/create');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    /**
     * @test
     *
     * @dataProvider provideBadData
     */
    public function shouldShowErrors(array $formData): void
    {
        $client = self::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/posts/create');

        $client->submitForm('Créer', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private static function createFormData(array $overrideData = []): array
    {
        $orignalFile = __DIR__ . '/../../public/uploads/image.png';

        $filename = sprintf('%s.png', Uuid::v4());

        $finalFile = sprintf('%s/../../public/uploads/%s', __DIR__, $filename);

        copy($orignalFile, $finalFile);

        return $overrideData + [
                'post[title]' => 'Title',
                'post[content]' => 'Content',
                'post[category]' => 1,
                'post[tags]' => 'foo,bar',
                'post[imageFile]' => new UploadedFile($finalFile, $filename, 'image/png', null, true)
            ];
    }

    public function provideBadData(): Generator
    {
        yield 'empty title' => [self::createFormData(['post[title]' => ''])];
        yield 'empty content' => [self::createFormData(['post[content]' => ''])];
        yield 'empty tags' => [self::createFormData(['post[tags]' => ''])];
        yield 'no image' => [self::createFormData(['post[imageFile]' => null])];
    }
}
