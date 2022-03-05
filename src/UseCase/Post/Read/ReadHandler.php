<?php

declare(strict_types=1);

namespace App\UseCase\Post\Read;

use App\Entity\Post;
use App\Messenger\Query;
use App\Messenger\QueryHandlerInterface;
use App\Repository\PostRepository;

final class ReadHandler implements QueryHandlerInterface
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function __invoke(ReadQuery $query): Post
    {
        return $this->postRepository->find($query->id);
    }
}
