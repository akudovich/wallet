<?php

declare(strict_types=1);

namespace App\Wallet\Application\Service;

use App\Wallet\Application\DTO\BalanceView;
use App\Wallet\Application\WalletQueryRepository;

final readonly class GetBalanceService
{
    public function __construct(
        private WalletQueryRepository $repository
    ) {}

    public function getBalance(int $id): BalanceView
    {
        return $this->repository->getBalance($id) ?? BalanceView::zero($id);
    }
}
