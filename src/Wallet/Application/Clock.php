<?php

declare(strict_types=1);

namespace App\Wallet\Application;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
