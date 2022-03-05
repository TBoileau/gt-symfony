<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class PostListTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldListPaginatedPosts(): void
    {
        $client = self::createClient();

        $crawler = $client->request(Request::METHOD_GET, '/posts');

        self::assertCount(18, $crawler->filter('article'));

        $client->clickLink("2");

        self::assertEquals(2, $client->getRequest()->query->getInt('page'));

        self::assertCount(18, $crawler->filter('article'));
    }
}
