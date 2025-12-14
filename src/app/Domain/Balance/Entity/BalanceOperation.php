<?php

namespace App\Domain\Balance\Entity;

use App\Domain\Shared\ValueObject\Money;
use InvalidArgumentException;

readonly class BalanceOperation
{
    public function __construct(
        private int $userId,
        private Money $amount,
        private string $description,
    ) {
        if ($description === '') {
            throw new InvalidArgumentException('Description must not be empty.');
        }
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
