<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Transaction;

use App\Wallet\Domain\Money;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\DatePoint;

#[ORM\Entity]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $walletId;

    #[ORM\Column(enumType: TransactionType::class)]
    private TransactionType $type;

    #[ORM\Column(enumType: TransactionReason::class)]
    private TransactionReason $reason;

    #[ORM\Embedded(class: Money::class)]
    private Money $amount;

    #[ORM\Column(type: 'date_point')]
    private DatePoint $createdAt;

    public function __construct(int $walletId, TransactionType $type, TransactionReason $reason, Money $amount)
    {
        $this->walletId = $walletId;
        $this->type = $type;
        $this->reason = $reason;
        $this->amount = $amount;
        $this->createdAt = (new Clock())->now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWalletId(): int
    {
        return $this->walletId;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getReason(): TransactionReason
    {
        return $this->reason;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCreatedAt(): DatePoint
    {
        return $this->createdAt;
    }
}
