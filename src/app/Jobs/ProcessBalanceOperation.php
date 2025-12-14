<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProcessBalanceOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;

    public string $amount;

    public string $description;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, float|string $amount, string $description)
    {
        $this->userId = $userId;
        $this->amount = $this->formatAmount($amount);
        $this->description = $description;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return list<WithoutOverlapping>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping("user-balance-{$this->userId}")];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            $user = User::lockForUpdate()->findOrFail($this->userId);

            $deltaAmount = $this->normalizeAmount($this->amount);

            if ($deltaAmount === 0.0) {
                throw new RuntimeException('Amount must not be zero.');
            }

            $currentBalance = $this->normalizeAmount($user->operations()->sum('amount'));
            $newBalance = $this->normalizeAmount($currentBalance + $deltaAmount);

            if ($newBalance < 0) {
                throw new RuntimeException('Operation aborted: resulting balance would be negative.');
            }

            $user->operations()->create([
                'amount' => $this->formatAmount($deltaAmount),
                'description' => $this->description,
            ]);
        });
    }

    private function normalizeAmount(float|string $value): float
    {
        return (float) number_format((float) $value, 2, '.', '');
    }

    private function formatAmount(float|string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
