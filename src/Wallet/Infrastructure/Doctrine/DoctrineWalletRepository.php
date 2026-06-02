<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Doctrine;

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
}
