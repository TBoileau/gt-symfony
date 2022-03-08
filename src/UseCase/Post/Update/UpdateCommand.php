<?php

declare(strict_types=1);

namespace App\UseCase\Post\Update;

use App\Entity\Post;

final class UpdateCommand
{
    public function __construct(public Post $post)
    {
    }
}
