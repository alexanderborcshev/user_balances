<?php

namespace App\Domain\Balance\Service;

use App\Domain\Balance\Exception\NegativeBalanceException;
use App\Domain\Balance\Exception\ZeroAmountException;
use App\Domain\Shared\ValueObject\Money;

class BalanceCalculator
{
    /**
     * @throws ZeroAmountException
     * @throws NegativeBalanceException
     * */
    public function assertCanApply(Money $currentBalance, Money $delta): Money
    {
        if ($delta->isZero()) {
            throw new ZeroAmountException('Amount must not be zero.');
        }

        $newBalance = $currentBalance->add($delta);

        if ($newBalance->isNegative()) {
            throw new NegativeBalanceException('Operation aborted: resulting balance would be negative.');
        }

        return $newBalance;
    }
}
