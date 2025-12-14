<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OperationController extends Controller
{
    public function index(Request $request): LengthAwarePaginator
    {
        $validated = $request->validate([
            'page' => ['integer', 'min:1'],
            'per_page' => ['integer', 'min:1', 'max:100'],
            'sort' => ['in:date'],
            'dir' => ['in:asc,desc'],
            'q' => ['string'],
        ]);

        $dir = $validated['dir'] ?? 'desc';
        $perPage = $validated['per_page'] ?? 15;
        $query = $request->user()
            ->operations()
            ->when(! empty($validated['q']), function ($q) use ($validated) {
                $search = mb_strtolower($validated['q']);
                $q->whereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            })
            ->orderBy('created_at', $dir);

        return $query->paginate($perPage);
    }

    public function latest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['integer', 'min:1', 'max:50'],
        ]);

        $limit = $validated['limit'] ?? 5;

        $operations = $request->user()
            ->operations()
            ->latest('created_at')
            ->limit($limit)
            ->get();

        return response()->json($operations);
    }
}