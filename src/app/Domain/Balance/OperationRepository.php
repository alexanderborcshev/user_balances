<?php

namespace App\Domain\Balance;

use App\Domain\Balance\Entity\BalanceOperation;
use App\Domain\Shared\ValueObject\Money;

interface OperationRepository
{
    public function sumByUserId(int $userId): Money;

    public function store(BalanceOperation $operation): void;

    public function getListUserId(
        int $userId,
        int $limit,
        string $orderBy = 'created_at',
        string $orderDir = 'desc'
    ): array;
}
