<?php

declare(strict_types=1);

namespace App\UseCase\Post\Update;

use App\Messenger\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;

final class UpdateHandler implements CommandHandlerInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private string $uploadDir) {
    }

    public function __invoke(UpdateCommand $command): void
    {
        $post = $command->post;

        if ($post->getImageFile() !== null) {
            $originalImage = $post->getImage();
            $post->setImage(
                sprintf(
                    '%s.%s',
                    Uuid::v4(),
                    $post->getImageFile()->getClientOriginalExtension()
                )
            );
            $post->getImageFile()->move($this->uploadDir, $post->getImage());
            $fileSystem = new Filesystem();
            $fileSystem->remove(sprintf('%s/%s', $this->uploadDir, $originalImage));
        }

        $this->entityManager->flush();
    }
}
