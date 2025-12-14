<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $balance = $request->user()->balance;

        return response()->json([
            'balance' => $balance,
        ]);
    }
}