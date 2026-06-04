<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Time;

use App\Wallet\Application\Clock;
use DateTimeImmutable;

final readonly class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
