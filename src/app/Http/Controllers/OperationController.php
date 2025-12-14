<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperationLatestRequest;
use App\Http\Requests\OperationRequest;
use App\Http\Resources\OperationsLatestResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class OperationController extends Controller
{
    public function index(OperationRequest $request): LengthAwarePaginator
    {
        $validated = $request->validated();

        $query = $request->user()
            ->operations()
            ->when(! empty($validated['q']), function ($q) use ($validated) {
                $search = mb_strtolower($validated['q']);
                $q->whereRaw('LOWER(description) LIKE ?', ["%$search%"]);
            })
            ->orderBy('created_at', $validated['dir']);

        return $query->paginate($validated['per_page']);
    }

    public function latest(OperationLatestRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $operations = $request->user()
            ->operations()
            ->latest('created_at')
            ->limit($validated['limit'])
            ->get();
        OperationsLatestResource::withoutWrapping();
        return OperationsLatestResource::collection($operations)->response();
    }
}