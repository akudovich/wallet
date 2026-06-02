<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use App\Wallet\Application\DTO\BalanceView;

interface WalletQueryRepository
{
    public function getBalance(int $id): BalanceView|null;
}
