<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\OperationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/balance', BalanceController::class);

    Route::get('/operations', [OperationController::class, 'index']);
    Route::get('/operations/latest', [OperationController::class, 'latest']);
});