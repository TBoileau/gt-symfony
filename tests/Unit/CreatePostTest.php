<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\UseCase\Post\Create\CreateCommand;
use App\UseCase\Post\Create\CreateHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

final class CreatePostTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnNewPost(): void
    {
        $imageFile = self::createMock(UploadedFile::class);

        $imageFile->method('getClientOriginalExtension')->willReturn('png');

        $post = new Post();
        $post->setImageFile($imageFile);
        $post->setTitle('Title');
        $post->setContent('Content');
        $post->setCategory(new Category());
        $post->getTags()->add(new Tag());

        $entityManager = self::createMock(EntityManagerInterface::class);

        $entityManager
            ->expects(self::once())
            ->method('persist')
            ->with(self::equalTo($post));

        $entityManager
            ->expects(self::once())
            ->method('flush');

        $security = self::createMock(Security::class);

        $user = new User();

        $security
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($user);

        $handler = new CreateHandler($entityManager, '', $security);

        $command = new CreateCommand($post);

        $post = $handler($command);

        self::assertMatchesRegularExpression('/^[a-z0-9-]+.png$/', $post->getImage());
        self::assertEquals($user, $post->getUser());
    }
}
