<?php

namespace App\Domain\Balance;

use App\Domain\Balance\Entity\BalanceOperation;
use App\Domain\Shared\ValueObject\Money;
use Illuminate\Pagination\LengthAwarePaginator;

interface OperationRepository
{
    public function sumByUserId(int $userId): Money;

    public function store(BalanceOperation $operation): void;

    public function getListByUserId(
        int $userId,
        int $limit,
        string $orderBy = 'created_at',
        string $orderDir = 'desc'
    ): array;
    public function getListByUserIdWithPagination(
        int $userId,
        int $per_page = 10,
        string $orderBy = 'created_at',
        string $orderDir = 'desc',
        string $search = ''
    ): LengthAwarePaginator;
}
