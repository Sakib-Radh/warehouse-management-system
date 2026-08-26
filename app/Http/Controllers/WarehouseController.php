<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $warehouses = $this->warehouseService->getAllWarehouses($perPage);
        return WarehouseResource::collection($warehouses);
    }

    public function store(WarehouseRequest $request): WarehouseResource
    {
        $warehouse = $this->warehouseService->createWarehouse($request->validated());
        return new WarehouseResource($warehouse);
    }

    public function show(int $id): WarehouseResource|JsonResponse
    {
        $warehouse = $this->warehouseService->getWarehouse($id);

        if (! $warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        return new WarehouseResource($warehouse);
    }

    public function update(WarehouseRequest $request, int $id): WarehouseResource|JsonResponse
    {
        $updated = $this->warehouseService->updateWarehouse($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        $warehouse = $this->warehouseService->getWarehouse($id);
        return new WarehouseResource($warehouse);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->warehouseService->deleteWarehouse($id);

        if (! $deleted) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        return response()->json(null, 204);
    }
}
