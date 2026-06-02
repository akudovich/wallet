<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

interface WalletRepository
{
    public function find(int $id): Wallet|null;

    public function debit(int $id, Money $amount): void;

    public function credit(int $id, Money $amount): void;
}
