<?php

namespace App\Application\Shared;

interface TransactionManager
{
    public function run(callable $callback);
}
