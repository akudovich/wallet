<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Transaction;

enum TransactionType: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
