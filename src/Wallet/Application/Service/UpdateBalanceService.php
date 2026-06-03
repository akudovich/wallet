<?php

declare(strict_types=1);

namespace App\Wallet\Application\Service;

use App\Wallet\Application\Command\UpdateBalance;
use App\Wallet\Application\TransactionManager;
use App\Wallet\Domain\Transaction\Transaction;
use App\Wallet\Domain\Transaction\TransactionType;
use App\Wallet\Domain\TransactionRepository;
use App\Wallet\Domain\WalletRepository;

final readonly class UpdateBalanceService
{
    public function __construct(
        private WalletRepository $repository,
        private TransactionRepository $transactionRepository,
        private TransactionManager $transactionManager,
    ) {}

    public function update(UpdateBalance $command): void
    {
        $transaction = new Transaction(
            walletId: $command->id,
            type: $command->type,
            reason: $command->reason,
            amount: $command->amount,
        );

        $this->transactionManager->transactional(function () use ($transaction): void {
            $this->transactionRepository->save($transaction);
            match ($transaction->type) {
                TransactionType::DEBIT => $this->repository->debit(
                    id: $transaction->walletId,
                    amount: $transaction->amount,
                ),
                TransactionType::CREDIT => $this->repository->credit(
                    id: $transaction->walletId,
                    amount: $transaction->amount,
                ),
            };
        });
    }
}
