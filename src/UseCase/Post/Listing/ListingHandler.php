<?php

declare(strict_types=1);

namespace App\UseCase\Post\Listing;

use App\Messenger\Query;
use App\Messenger\QueryHandlerInterface;
use App\Repository\PostRepository;

final class ListingHandler implements QueryHandlerInterface
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function __invoke(ListingQuery $query): Listing
    {
        return new Listing(
            $this->postRepository->getPaginatedPosts($query->page),
            $query->page
        );
    }
}
