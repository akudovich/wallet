<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Exception;

final class InsufficientFunds extends DomainException
{
    public static function forWallet(int $id, int $requestedAmount, int $availableAmount = 0): self
    {
        return new self(sprintf(
            'Insufficient funds for wallet id: %d. Requested: %d, available: %d.',
            $id,
            $requestedAmount,
            $availableAmount,
        ));
    }
}
