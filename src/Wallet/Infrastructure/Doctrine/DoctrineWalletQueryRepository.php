<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Doctrine;

use App\Wallet\Application\DTO\BalanceView;
use App\Wallet\Domain\Currency;
use App\Wallet\Domain\WalletQueryRepository;
use Doctrine\DBAL\Connection;

final readonly class DoctrineWalletQueryRepository implements WalletQueryRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function getBalance(int $id): BalanceView|null
    {
        $row = $this->connection
            ->fetchAssociative(
                'SELECT id, balance_amount, balance_currency FROM wallet WHERE id = :id',
                [
                    'id' => $id,
                ],
            );

        if ($row === false) {
            return null;
        }

        return $this->hydrateBalanceView($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrateBalanceView(array $row): BalanceView
    {

        $id = $row['id'] ?? null;

        $amount = $row['balance_amount'] ?? null;

        $currency = $row['balance_currency'] ?? null;

        if (! is_numeric($id)) {

            throw new \UnexpectedValueException('Invalid id returned from database.');
        }

        if (! is_numeric($amount)) {

            throw new \UnexpectedValueException('Invalid amount returned from database.');
        }

        if (! is_string($currency)) {

            throw new \UnexpectedValueException('Invalid currency returned from database.');
        }

        $currencyEnum = Currency::tryFrom($currency);
        if ($currencyEnum === null) {
            throw new \UnexpectedValueException(
                sprintf('Unknown currency "%s" returned from database.', $currency)
            );
        }
        return new BalanceView(id: (int) $id, balance: (int) $amount, currency: $currencyEnum);
    }
}
