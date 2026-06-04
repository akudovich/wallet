<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Transaction;

use App\Wallet\Domain\Money;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Transaction
{
    #[ORM\Column]
    public int $walletId {
        get {
            return $this->walletId;
        }
    }

    #[ORM\Column(enumType: TransactionType::class)]
    public TransactionType $type {
        get {
            return $this->type;
        }
    }

    #[ORM\Column(enumType: TransactionReason::class)]
    public TransactionReason $reason {
        get {
            return $this->reason;
        }
    }

    #[ORM\Embedded(class: Money::class)]
    public Money $amount {
        get {
            return $this->amount;
        }
    }

    #[ORM\Column(type: 'datetime_immutable')]
    public DateTimeImmutable $createdAt {
        get {
            return $this->createdAt;
        }
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null {
        get {
            return $this->id;
        }
    }

    public function __construct(
        int $walletId,
        TransactionType $type,
        TransactionReason $reason,
        Money $amount,
        DateTimeImmutable $createdAt
    ) {
        $this->walletId = $walletId;
        $this->type = $type;
        $this->reason = $reason;
        $this->amount = $amount;
        $this->createdAt = $createdAt;
    }
}
