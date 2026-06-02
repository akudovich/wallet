<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Exception;

use App\Wallet\Domain\Currency;

class CurrencyMismatch extends DomainException
{
    public static function between(Currency $left, Currency $right): self
    {
        return new self(sprintf('Currency mismatch: %s vs %s', $left->value, $right->value));
    }
}
