<?php

declare(strict_types=1);

namespace App\Messenger;

interface QueryBusInterface
{
    public function fetch(mixed $query): mixed;
}
