<?php

namespace App\Application\Balance\ApplyBalanceOperation;

readonly class ApplyBalanceOperationCommand
{
    public function __construct(
        public int $userId,
        public float|string $amount,
        public string $description,
    ) {
    }
}
