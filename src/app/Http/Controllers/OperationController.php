<?php

namespace App\Http\Controllers;

use App\Domain\Balance\OperationRepository;
use App\Http\Requests\OperationLatestRequest;
use App\Http\Requests\OperationRequest;
use App\Http\Resources\OperationsLatestResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class OperationController extends Controller
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
    ) {
    }

    public function index(OperationRequest $request): LengthAwarePaginator
    {
        $validated = $request->validated();

        return $this->operationRepository->getListByUserIdWithPagination(
            userId: $request->user()->id,
            per_page:  (int) $validated['per_page'],
            orderDir: $validated['dir'],
            search: $validated['q'] ?? '',
        );
    }

    public function latest(OperationLatestRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $operations = $this->operationRepository->getListByUserId(
            $request->user()->id,
            (int) $validated['limit']
        );

        OperationsLatestResource::withoutWrapping();
        return OperationsLatestResource::collection($operations)->response();
    }
}