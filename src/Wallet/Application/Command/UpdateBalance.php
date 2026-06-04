<?php

declare(strict_types=1);

namespace App\Wallet\Application\Command;

use App\Wallet\Domain\Currency;
use App\Wallet\Domain\Money;
use App\Wallet\Domain\Transaction\TransactionReason;
use App\Wallet\Domain\Transaction\TransactionType;

final readonly class UpdateBalance
{
    public function __construct(
        public int $id,
        public TransactionType $type,
        public TransactionReason $reason,
        public Money $amount,
    ) {}

    public static function create(
        int $id,
        TransactionType $type,
        TransactionReason $reason,
        int $amount,
        Currency $currency
    ): self {
        return new self(id: $id, type: $type, reason: $reason, amount: new Money($amount, $currency));
    }
}
