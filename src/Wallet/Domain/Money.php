<?php

declare(strict_types=1);

namespace App\Wallet\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Money
{
    public function __construct(
        #[ORM\Column]
        public int $amount,
        #[ORM\Column(length: 3, enumType: Currency::class)]
        public Currency $currency
    ) {}
}
