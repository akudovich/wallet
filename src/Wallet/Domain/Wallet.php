<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final readonly class Wallet
{
    private function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private int $id,
        #[ORM\Embedded(class: Money::class)]
        private Money $balance,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }
}
