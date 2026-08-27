<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockMovementIndexRequest;
use App\Http\Requests\StockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Services\StockMovementService;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    protected StockMovementService $stockMovementService;

    /**
     * Create a new controller instance.
     */
    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    /**
     * Display a listing of stock movements.
     */
    public function index(StockMovementIndexRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $filters = $request->validated();
        $perPage = (int) $request->query('per_page', 15);

        $movements = $this->stockMovementService->getStockMovements($filters, $perPage);

        return StockMovementResource::collection($movements);
    }

    /**
     * Store a newly created stock movement in storage.
     */
    public function store(StockMovementRequest $request): StockMovementResource|JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $movement = $this->stockMovementService->processMovement($data);

            return new StockMovementResource($movement);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process stock movement',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
