<?php

namespace App\Http\Controllers;

use App\Domain\Balance\OperationRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $balance = $this->operationRepository->sumByUserId($request->user()->id);

        return response()->json([
            'balance' => $balance->format(),
        ]);
    }
}