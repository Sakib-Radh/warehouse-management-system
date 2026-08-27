<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Inventory;
use App\Models\StockMovement;
use App\Jobs\CheckLowStockAlertJob;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class StockMovementService extends BaseService
{
    protected StockMovementRepositoryInterface $stockMovementRepository;
    protected InventoryService $inventoryService;

    public function __construct(
        StockMovementRepositoryInterface $stockMovementRepository,
        InventoryService $inventoryService
    ) {
        $this->stockMovementRepository = $stockMovementRepository;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process a stock movement and update inventory accordingly.
     */
    public function processMovement(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $type = $data['type'];
            $productId = $data['product_id'];
            $qty = $data['quantity'];
            
            $this->executeMovementType($type, $productId, $qty, $data);

            $movement = $this->stockMovementRepository->create($data);
            $this->inventoryService->invalidateCache();

            if (in_array($type, ['dispatch', 'transfer'])) {
                CheckLowStockAlertJob::dispatch($productId);
            }

            return $movement;
        });
    }

    /**
     * Update the inventory quantity using pessimistic locking to prevent race conditions.
     */
    protected function updateInventory(int $productId, int $locationId, int $quantityChange): void
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->lockForUpdate()
            ->first();

        if ($inventory) {
            $newQuantity = $inventory->quantity + $quantityChange;

            if ($newQuantity < 0) {
                throw new Exception('Insufficient stock in location to perform this operation.');
            }

            $inventory->update(['quantity' => $newQuantity]);
            
            return;
        }

        if ($quantityChange < 0) {
            throw new Exception('Insufficient stock in location to perform this operation.');
        }

        Inventory::create([
            'product_id' => $productId,
            'location_id' => $locationId,
            'quantity' => $quantityChange,
        ]);
    }

    /**
     * Execute the specific logic for each movement type.
     */
    protected function executeMovementType(string $type, int $productId, int $qty, array $data): void
    {
        if ($type === 'receive') {
            $this->updateInventory($productId, $data['destination_location_id'], $qty);
            
            return;
        }

        if ($type === 'dispatch') {
            $this->updateInventory($productId, $data['source_location_id'], -$qty);
            
            return;
        }

        if ($type === 'transfer') {
            $this->updateInventory($productId, $data['source_location_id'], -$qty);
            $this->updateInventory($productId, $data['destination_location_id'], $qty);
        }
    }

    /**
     * Get paginated and filtered stock movements.
     */
    public function getStockMovements(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockMovementRepository->getFiltered($filters, $perPage);
    }
}
