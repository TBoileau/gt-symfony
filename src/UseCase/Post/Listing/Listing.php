<?php

declare(strict_types=1);

namespace App\UseCase\Post\Listing;

use App\Messenger\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class Listing
{
    public function __construct(public Paginator $posts, private int $page)
    {
    }

    /**
     * @return array{page: int, pages: int, range: array<array-key, int>}
     */
    public function getPagination(): array
    {
        return [
            'page' => $this->page,
            'pages' => ceil($this->posts->count() / 18),
            'range' => range(
                max(1, $this->page - 3),
                min(ceil($this->posts->count() / 18), $this->page + 3)
            )
        ];
    }
}
