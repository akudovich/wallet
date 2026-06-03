<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use Psr\Clock\ClockInterface;

final readonly class Clock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
