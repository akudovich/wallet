<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Doctrine;

use App\Wallet\Domain\Exception\CurrencyMismatch;
use App\Wallet\Domain\Exception\InsufficientFunds;
use App\Wallet\Domain\Money;
use App\Wallet\Domain\Wallet;
use App\Wallet\Domain\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineWalletRepository implements WalletRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function save(Wallet $wallet): void
    {
        $this->em->persist($wallet);
    }

    public function find(int $id): ?Wallet
    {
        return $this->em->find(Wallet::class, $id);
    }

    public function debit(int $id, Money $amount): void
    {
        $affected = $this->em->getConnection()
            ->executeStatement(
                'INSERT INTO wallet (id, balance_amount, balance_currency) VALUES (:id, :amount, :currency) ON CONFLICT (id) DO UPDATE SET balance_amount = wallet.balance_amount + EXCLUDED.balance_amount WHERE wallet.balance_currency = EXCLUDED.balance_currency',
                [
                    'id' => $id,
                    'amount' => $amount->amount,
                    'currency' => $amount->currency
                        ->value,
                ],
            );
        if ($affected === 0) {
            $wallet = $this->find($id);
            if ($wallet === null) {
                throw new \LogicException('Wallet not found');
            }
            throw CurrencyMismatch::between($wallet->balance->currency, $amount->currency);
        }
    }

    public function credit(int $id, Money $amount): void
    {
        $affected = $this->em->getConnection()
            ->executeStatement(
                'UPDATE wallet SET balance_amount = balance_amount - :amount WHERE id = :id AND balance_currency = :currency AND balance_amount >= :amount',
                [
                    'id' => $id,
                    'amount' => $amount->amount,
                    'currency' => $amount->currency
                        ->value,
                ],
            );
        if ($affected === 0) {
            $wallet = $this->find($id);
            if ($wallet !== null && $wallet->balance->currency !== $amount->currency) {
                throw CurrencyMismatch::between($wallet->balance->currency, $amount->currency);
            }
            throw InsufficientFunds::forWallet($id, $amount->amount);
        }
    }
}
