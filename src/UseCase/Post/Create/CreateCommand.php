<?php

declare(strict_types=1);

namespace App\UseCase\Post\Create;

use App\Entity\Post;

final class CreateCommand
{
    public function __construct(public Post $post)
    {
    }
}
