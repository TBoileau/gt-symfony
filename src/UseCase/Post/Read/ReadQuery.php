<?php

declare(strict_types=1);

namespace App\UseCase\Post\Read;

final class ReadQuery
{
    public function __construct(public int $id)
    {
    }
}
