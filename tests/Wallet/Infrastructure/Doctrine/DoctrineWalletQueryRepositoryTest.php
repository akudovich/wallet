<?php

declare(strict_types=1);

namespace App\Tests\Wallet\Infrastructure\Doctrine;

use App\Wallet\Infrastructure\Doctrine\DoctrineWalletQueryRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DoctrineWalletQueryRepositoryTest extends TestCase
{
    public function testInvalidIdReturnedFromDatabaseFail(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with('SELECT id, balance_amount, balance_currency FROM wallet WHERE id = :id', [
                'id' => 1,
            ],)
            ->willReturn([
                'id' => 'invalid',
                'balance_amount' => 100,
                'balance_currency' => 'RUB',
            ]);

        $repository = new DoctrineWalletQueryRepository($connection);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid id returned from database.');

        $repository->getBalance(1);
    }

    public function testInvalidAmountReturnedFromDatabaseFail(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with('SELECT id, balance_amount, balance_currency FROM wallet WHERE id = :id', [
                'id' => 1,
            ],)
            ->willReturn([
                'id' => 1,
                'balance_amount' => 'invalid',
                'balance_currency' => 'RUB',
            ]);

        $repository = new DoctrineWalletQueryRepository($connection);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid amount returned from database.');

        $repository->getBalance(1);
    }

    public function testInvalidCurrencyReturnedFromDatabaseFail(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with('SELECT id, balance_amount, balance_currency FROM wallet WHERE id = :id', [
                'id' => 1,
            ],)
            ->willReturn([
                'id' => 1,
                'balance_amount' => 100,
                'balance_currency' => 123,
            ]);

        $repository = new DoctrineWalletQueryRepository($connection);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid currency returned from database.');

        $repository->getBalance(1);
    }

    public function testUnknownCurrencyReturnedFromDatabaseFail(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('fetchAssociative')
            ->with('SELECT id, balance_amount, balance_currency FROM wallet WHERE id = :id', [
                'id' => 1,
            ],)
            ->willReturn([
                'id' => 1,
                'balance_amount' => 100,
                'balance_currency' => 'UNKNOWN',
            ]);

        $repository = new DoctrineWalletQueryRepository($connection);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown currency "UNKNOWN" returned from database.');

        $repository->getBalance(1);
    }
}
