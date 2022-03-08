<?php

declare(strict_types=1);

namespace App\UseCase\Post\Listing;

use Doctrine\ORM\Tools\Pagination\Paginator;

final class Listing
{
    public function __construct(private Paginator $posts, private int $page)
    {
    }

    /**
     * @return array{page: int, pages: int, range: array<array-key, int>}
     */
    private function getPagination(): array
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

    /**
     * @return array{
     *      posts: Pagination<Post>,
     *      pagination: array{
     *          page: int,
     *          pages: int,
     *          range: array<array-key, int>
     *      }
     * }
     */
    public function getData(): array
    {
        return [
            'posts' => $this->posts,
            'pagination' => $this->getPagination()
        ];
    }
}
