<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use App\Wallet\Domain\Transaction\Transaction;

interface TransactionRepository
{
    public function last(): Transaction|null;

    public function save(Transaction $transaction): void;
}
