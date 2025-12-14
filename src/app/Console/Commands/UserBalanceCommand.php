<?php

namespace App\Console\Commands;

use App\Application\Balance\ApplyBalanceOperation\ApplyBalanceOperationCommand;
use App\Application\Balance\ApplyBalanceOperation\ApplyBalanceOperationHandler;
use App\Domain\Balance\Service\BalanceCalculator;
use App\Domain\Shared\ValueObject\Money;
use App\Jobs\ProcessBalanceOperation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:balance {email : Email of the user} {amount : Amount to apply (positive to credit, negative to debit)} {description : Description of the operation}';

    /**
     * The console command description.
     */
    protected $description = 'Apply a balance operation to a user';

    public function __construct(private readonly ApplyBalanceOperationHandler $handler)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $validated = $this->validateInput();

            if ($validated === null) {
                return self::FAILURE;
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $user = User::where('email', $validated['email'])->firstOrFail();

        $command = new ApplyBalanceOperationCommand(
            $user->id,
            $validated['amount'],
            $validated['description']
        );

        try {
            $deltaAmount = Money::fromNumeric($command->amount);
            $currentBalance = Money::fromNumeric($user->balance);
            $balanceCalculator = new BalanceCalculator();
            $newBalance = $balanceCalculator->assertCanApply($currentBalance, $deltaAmount);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        if ($newBalance->isNegative()) {
            $this->error("Operation aborted: resulting balance would be negative");
            return self::FAILURE;
        }

        ProcessBalanceOperation::dispatch(
            $command,
            $this->handler
        );

        $this->info("Operation queued for $user->email: {$deltaAmount->format()}");
        $this->info("Expected balance after processing: {$newBalance->format()}");

        return self::SUCCESS;
    }

    /**
     * @throws ValidationException
     */
    private function validateInput(): ?array
    {
        $validator = Validator::make([
            'email' => $this->argument('email'),
            'amount' => $this->argument('amount'),
            'description' => $this->argument('description'),
        ], [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return null;
        }

        return $validator->validated();
    }

}