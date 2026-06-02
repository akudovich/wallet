<?php

declare(strict_types=1);

namespace App\Wallet\Infrastructure\Doctrine;

use App\Wallet\Application\TransactionManager;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTransactionManager implements TransactionManager
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function transactional(callable $callback): mixed
    {
        return $this->em->wrapInTransaction($callback);
    }
}
