<?php

namespace App\Jobs;

use App\Application\Balance\ApplyBalanceOperation\ApplyBalanceOperationCommand;
use App\Application\Balance\ApplyBalanceOperation\ApplyBalanceOperationHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessBalanceOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly ApplyBalanceOperationCommand $command,
        private readonly ApplyBalanceOperationHandler $handler
    )
    {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return list<WithoutOverlapping>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping("user-balance-".$this->command->userId)];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->handler->handle($this->command);
    }
}
