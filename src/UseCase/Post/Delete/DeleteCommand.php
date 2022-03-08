<?php

declare(strict_types=1);

namespace App\UseCase\Post\Delete;

use App\Entity\Post;

final class DeleteCommand
{
    public function __construct(public Post $post)
    {
    }
}
