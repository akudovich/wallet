<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use App\Wallet\Domain\Exception\CurrencyMismatch;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Money
{
    public function __construct(
        #[ORM\Column]
        private int $amount,
        #[ORM\Column(length: 3, enumType: Currency::class)]
        private Currency $currency
    ) {}

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function add(self $addend): self
    {
        if ($this->currency !== $addend->currency) {
            throw CurrencyMismatch::between($this->currency, $addend->currency);
        }
        return new self(amount: $this->amount + $addend->amount, currency: $this->currency);
    }

    public function sub(self $refund): self
    {
        if ($this->currency !== $refund->currency) {
            throw CurrencyMismatch::between($this->currency, $refund->currency);
        }
        return new self(amount: $this->amount - $refund->amount, currency: $this->currency);
    }
}
