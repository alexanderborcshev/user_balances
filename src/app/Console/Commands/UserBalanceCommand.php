<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBalanceOperation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    /**
     * Execute the console command.
     * @throws ValidationException
     */
    public function handle(): int
    {
        $validated = $this->validateInput();

        if ($validated === null) {
            return self::FAILURE;
        }

        $user = User::where('email', $validated['email'])->firstOrFail();

        $deltaAmount = $this->normalizeAmount($validated['amount']);
        if (! $this->guardNonZeroAmount($deltaAmount)) {
            return self::FAILURE;
        }

        $currentBalance = $this->normalizeAmount($user->balance);
        $newBalance = $this->normalizeAmount($currentBalance + $deltaAmount);

        if ($newBalance < 0) {
            $formattedCurrent = $this->formatAmount($currentBalance);
            $formattedDelta = $this->formatAmount($deltaAmount);

            $this->error("Operation aborted: resulting balance would be negative (current $formattedCurrent, change $formattedDelta).");
            return self::FAILURE;
        }

        ProcessBalanceOperation::dispatch(
            $user->id,
            $this->formatAmount($deltaAmount),
            $validated['description']
        );

        $this->info("Operation queued for $user->email: {$this->formatAmount($deltaAmount)}");
        $this->info("Expected balance after processing: {$this->formatAmount($newBalance)}");

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

    private function guardNonZeroAmount(float $deltaAmount): bool
    {
        if ($deltaAmount === 0.0) {
            $this->error('Amount must not be zero.');
            return false;
        }

        return true;
    }

    private function normalizeAmount(float|string $value): float
    {
        return (float) number_format((float) $value, 2, '.', '');
    }

    private function formatAmount(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}