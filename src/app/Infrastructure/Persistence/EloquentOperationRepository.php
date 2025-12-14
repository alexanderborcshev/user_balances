<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Balance\Entity\BalanceOperation;
use App\Domain\Balance\OperationRepository;
use App\Domain\Shared\ValueObject\Money;
use App\Models\Operation;

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
}
