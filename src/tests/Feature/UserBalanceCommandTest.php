<?php

namespace Tests\Feature;

use App\Application\Balance\ApplyBalanceOperation\ApplyBalanceOperationCommand;
use App\Application\Balance\ApplyBalanceOperation\ApplyBalanceOperationHandler;
use App\Domain\Balance\Exception\NegativeBalanceException;
use App\Domain\Balance\Service\BalanceCalculator;
use App\Infrastructure\Persistence\EloquentOperationRepository;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use App\Jobs\ProcessBalanceOperation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Tests\TestCase;

class UserBalanceCommandTest extends TestCase
{
    public function test_it_credits_balance_and_creates_operation(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $this->artisan('user:balance', [
            'email' => $user->email,
            'amount' => '150.50',
            'description' => 'Deposit',
        ])->assertExitCode(CommandAlias::SUCCESS);

        Queue::assertPushed(ProcessBalanceOperation::class, function (ProcessBalanceOperation $job) use ($user): bool {
            $this->assertSame($user->id, $job->command->userId);
            $this->assertSame('150.50', $job->command->amount);
            $this->assertSame('Deposit', $job->command->description);

            app()->call([$job, 'handle']);

            return true;
        });

        $this->assertSame('150.50', $user->fresh()->balance);

        $this->assertDatabaseHas('operations', [
            'user_id' => $user->id,
            'amount' => 150.50,
            'description' => 'Deposit',
        ]);
    }

    public function test_it_blocks_operation_if_balance_would_be_negative(): void
    {
        $user = User::factory()->create();

        Queue::fake();

        $this->artisan('user:balance', [
            'email' => $user->email,
            'amount' => '-50',
            'description' => 'Withdraw',
        ])->assertExitCode(Command::FAILURE);

        Queue::assertNothingPushed();

        $this->assertDatabaseCount('operations', 0);
        $this->assertSame('0.00', $user->fresh()->balance);
    }

    public function test_it_allows_debit_when_balance_is_sufficient(): void
    {
        $user = User::factory()->create();

        $user->operations()->create([
            'amount' => 120,
            'description' => 'Initial top up',
        ]);

        Queue::fake();

        $this->artisan('user:balance', [
            'email' => $user->email,
            'amount' => '-20.25',
            'description' => 'Payment',
        ])->assertExitCode(CommandAlias::SUCCESS);

        Queue::assertPushed(ProcessBalanceOperation::class, function (ProcessBalanceOperation $job) use ($user): bool {
            $this->assertSame($user->id, $job->command->userId);
            $this->assertSame('-20.25', $job->command->amount);
            $this->assertSame('Payment', $job->command->description);

            app()->call([$job, 'handle']);

            return true;
        });

        $this->assertSame('99.75', $user->fresh()->balance);

        $this->assertDatabaseHas('operations', [
            'user_id' => $user->id,
            'amount' => -20.25,
            'description' => 'Payment',
        ]);
    }

    public function test_job_fails_if_resulting_balance_negative(): void
    {
        $user = User::factory()->create();

        $job = new ProcessBalanceOperation(
            new ApplyBalanceOperationCommand($user->id, -10, 'Attempt'),
            new ApplyBalanceOperationHandler(
                new EloquentUserRepository(),
                new EloquentOperationRepository(),
                new BalanceCalculator(),
                new LaravelTransactionManager()
            )
        );

        $this->expectException(NegativeBalanceException::class);
        $this->expectExceptionMessage('Operation aborted: resulting balance would be negative.');

        app()->call([$job, 'handle']);

        $this->assertDatabaseCount('operations', 0);
    }
}
