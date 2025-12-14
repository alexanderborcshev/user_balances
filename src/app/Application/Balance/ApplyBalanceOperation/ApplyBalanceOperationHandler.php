<?php

namespace App\Application\Balance\ApplyBalanceOperation;

use App\Application\Shared\TransactionManager;
use App\Domain\Balance\Entity\BalanceOperation;
use App\Domain\Balance\OperationRepository;
use App\Domain\Balance\Service\BalanceCalculator;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\User\UserRepository;
use LogicException;

readonly class ApplyBalanceOperationHandler
{
    public function __construct(
        private UserRepository $users,
        private OperationRepository $operations,
        private BalanceCalculator $calculator,
        private TransactionManager $transactions,
    ) {
    }

    public function handle(ApplyBalanceOperationCommand $command): Money
    {
        return $this->transactions->run(function () use ($command) {
            $user = $this->users->getByIdWithLock($command->userId);

            $userId = $user->getId();
            if ($userId === null) {
                throw new LogicException('Persisted user must have an id.');
            }

            $delta = Money::fromNumeric($command->amount);
            $currentBalance = $this->operations->sumByUserId($userId);
            $newBalance = $this->calculator->assertCanApply($currentBalance, $delta);

            $operation = new BalanceOperation($userId, $delta, $command->description);
            $this->operations->store($operation);

            return $newBalance;
        });
    }
}
