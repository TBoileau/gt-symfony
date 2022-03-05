<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profile;

final class LoginTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldAuthenticate(): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', self::createFormData());

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            self::assertTrue($securityCollector->isAuthenticated());
        }

        $client->followRedirect();

        self::assertRouteSame('post_list');
    }

    /**
     * @test
     *
     * @dataProvider provideBadData
     */
    public function shouldShowErrors(array $formData): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/login');

        $client->submitForm('Se connecter', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->enableProfiler();

        if (($profile = $client->getProfile()) instanceof Profile) {
            /** @var SecurityDataCollector $securityCollector */
            $securityCollector = $profile->getCollector('security');

            self::assertFalse($securityCollector->isAuthenticated());
        }

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function provideBadData(): Generator
    {
        yield 'bad nickname' => [self::createFormData(['nickname' => 'fail'])];
        yield 'bad password' => [self::createFormData(['password' => 'fail'])];
    }

    private static function createFormData(array $overrideData = []): array
    {
        return $overrideData + [
            'nickname' => 'user+1',
            'password' => 'password'
        ];
    }
}
