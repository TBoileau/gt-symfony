<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class PostReadTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldReadPost(): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/posts/1/read');

        self::assertResponseIsSuccessful();
    }
}
