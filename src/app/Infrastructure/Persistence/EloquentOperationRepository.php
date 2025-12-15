<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Balance\Entity\BalanceOperation;
use App\Domain\Balance\OperationRepository;
use App\Domain\Shared\ValueObject\Money;
use App\Models\Operation;
use Illuminate\Pagination\LengthAwarePaginator;


class EloquentOperationRepository implements OperationRepository
{
    public function sumByUserId(int $userId): Money
    {
        $sum = Operation::where('user_id', $userId)->sum('amount');

        return Money::fromNumeric($sum);
    }

    public function store(BalanceOperation $operation): void
    {
        Operation::create([
            'user_id' => $operation->getUserId(),
            'amount' => $operation->getAmount()->format(),
            'description' => $operation->getDescription(),
        ]);
    }

    public function getListByUserId(
        int $userId,
        int $limit,
        string $orderBy = 'created_at',
        string $orderDir = 'desc'
    ): array
    {
        $operations = Operation::where('user_id', $userId)
            ->orderBy($orderBy, $orderDir)
            ->limit($limit)
            ->get()
            ->toArray();

        return array_map(static fn ($operation) =>
            new BalanceOperation(
                $operation['user_id'],
                Money::fromNumeric($operation['amount']),
                $operation['description'],
            ),
            $operations);
    }

    public function getListByUserIdWithPagination(
        int $userId,
        int $per_page = 10,
        string $orderBy = 'created_at',
        string $orderDir = 'desc',
        string $search = ''
    ): LengthAwarePaginator
    {
        $query = Operation::where('user_id', $userId)
        ->when(! empty($search), function ($q) use ($search) {
            $search = mb_strtolower($search);
            $q->whereRaw('LOWER(description) LIKE ?', ["%$search%"]);
        })
        ->orderBy($orderBy, $orderDir);

        return $query->paginate($per_page);
    }

}
