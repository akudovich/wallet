<?php

declare(strict_types=1);

namespace App\Wallet\UI\Http\UpdateService;

use App\Wallet\Domain\Currency;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class RequestBody
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $amount,
        #[Assert\NotBlank]
        public Currency $currency,
    ) {}
}
