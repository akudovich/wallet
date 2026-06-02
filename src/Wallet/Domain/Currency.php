<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

enum Currency: string
{
    case USD = 'USD';
    case RUB = 'RUB';

    public static function default(): self
    {
        return self::RUB;
    }
}
