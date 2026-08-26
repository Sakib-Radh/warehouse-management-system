<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Services\InventoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    /**
     * Create a new controller instance.
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the inventory.
     */
    public function index(InventoryRequest $request): AnonymousResourceCollection
    {
        $filters = $request->only(['product_id', 'location_id']);
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $inventories = $this->inventoryService->getInventory($filters, $perPage, $page);

        return InventoryResource::collection($inventories);
    }
}
