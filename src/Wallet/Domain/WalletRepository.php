<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

interface WalletRepository
{
    public function find(int $id): Wallet|null;

    public function save(Wallet $wallet): void;
}
