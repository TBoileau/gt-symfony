<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PostDeleteTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldDeletePost(): void
    {
        $client = self::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->find(1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/posts/1/read');

        self::assertResponseIsSuccessful();

        $client->submitForm('Supprimer');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('post_list');
        /** @var PostRepository $postRepository */
        $postRepository = $client->getContainer()->get(PostRepository::class);

        /** @var Post|null $post */
        $post = $postRepository->find(1);

        self::assertNull($post);
    }
}
