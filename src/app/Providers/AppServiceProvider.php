<?php

namespace App\Providers;

use App\Application\Shared\TransactionManager;
use App\Domain\Balance\OperationRepository;
use App\Domain\Shared\Service\PasswordHasher;
use App\Domain\User\UserRepository;
use App\Infrastructure\Hashing\LaravelPasswordHasher;
use App\Infrastructure\Persistence\EloquentOperationRepository;
use App\Infrastructure\Persistence\EloquentUserRepository;
use App\Infrastructure\Persistence\LaravelTransactionManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class,  EloquentUserRepository::class);
        $this->app->bind(OperationRepository::class,  EloquentOperationRepository::class);
        $this->app->bind(PasswordHasher::class,  LaravelPasswordHasher::class);
        $this->app->bind(TransactionManager::class,  LaravelTransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
