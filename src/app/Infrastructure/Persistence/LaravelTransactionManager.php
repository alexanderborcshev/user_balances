<?php

namespace App\Infrastructure\Persistence;

use App\Application\Shared\TransactionManager;
use Illuminate\Support\Facades\DB;

class LaravelTransactionManager implements TransactionManager
{
    public function run(callable $callback)
    {
        return DB::transaction($callback);
    }
}