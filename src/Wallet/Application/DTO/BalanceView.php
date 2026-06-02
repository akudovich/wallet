<?php

declare(strict_types=1);

namespace App\Wallet\Application\DTO;

use App\Wallet\Domain\Currency;

final readonly class BalanceView
{
    public function __construct(
        public int $id,
        public int $balance,
        public Currency $currency
    ) {}

    public static function zero(int $id): self
    {
        return new self(id: $id, balance: 0, currency: Currency::default());
    }
}
