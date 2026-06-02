<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Wallet
{
    public function __construct(
        #[ORM\Embedded(class: Money::class)]
        private Money $balance,
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private int|null $id = null,
    ) {}

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function stock(Money $stock): void
    {
        $this->balance = $this->balance->add($stock);
    }

    public function refund(Money $refund): void
    {
        $this->balance = $this->balance->sub($refund);
    }
}
