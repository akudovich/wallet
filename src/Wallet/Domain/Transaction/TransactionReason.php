<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Transaction;

enum TransactionReason: string
{
    case STOCK = 'stock';
    case REFUND = 'refund';
}
