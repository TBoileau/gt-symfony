<?php

declare(strict_types=1);

namespace App\UseCase\Post\Delete;

use App\Messenger\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;

final class DeleteHandler implements CommandHandlerInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private string $uploadDir) {
    }

    public function __invoke(DeleteCommand $command): void
    {
        $post = $command->post;
        $fileSystem = new Filesystem();
        $fileSystem->remove(sprintf('%s/%s', $this->uploadDir, $post->getImage()));
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}
