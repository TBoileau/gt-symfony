<?php

declare(strict_types=1);

namespace App\UseCase\Post\Listing;

final class ListingQuery
{
    public function __construct(public int $page)
    {
    }
}
