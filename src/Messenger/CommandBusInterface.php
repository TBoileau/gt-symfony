<?php

declare(strict_types=1);

namespace App\Messenger;

interface CommandBusInterface
{
    public function dispatch(mixed $command): mixed;
}
