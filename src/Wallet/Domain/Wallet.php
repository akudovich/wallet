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
        public int $id,
        #[ORM\Embedded(class: Money::class)]
        public Money $balance,
    ) {}
}
