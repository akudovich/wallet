<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Doctrine;

use App\Wallet\Domain\Transaction\Transaction;
use App\Wallet\Domain\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTransactionRepository implements TransactionRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Transaction $transaction): void
    {
        $this->em->persist($transaction);
    }
}
