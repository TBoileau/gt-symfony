<?php

declare(strict_types=1);

namespace App\UseCase\Post\Create;

use App\Entity\Post;
use App\Entity\User;
use App\Messenger\CommandHandlerInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

final class CreateHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $uploadDir,
        private Security $security
    ) {
    }

    public function __invoke(CreateCommand $command): Post
    {
        $post = $command->post;
        /** @var User $user */
        $user = $this->security->getUser();
        $post->setUser($user);
        $post->setPublishedAt(new DateTimeImmutable());
        $post->setImage(
            sprintf(
                '%s.%s',
                Uuid::v4(),
                $post->getImageFile()->getClientOriginalExtension()
            )
        );
        $post->getImageFile()->move($this->uploadDir, $post->getImage());
        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $post;
    }
}
