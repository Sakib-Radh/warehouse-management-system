<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\StockMovement;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    public function __construct(StockMovement $model)
    {
        parent::__construct($model);
    }

    /**
     * Get filtered stock movements.
     */
    public function getFiltered(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['warehouse_id'])) {
            $warehouseId = $filters['warehouse_id'];
            $query->where(function ($q) use ($warehouseId) {
                $q->whereHas('sourceLocation', function ($subQ) use ($warehouseId) {
                    $subQ->where('warehouse_id', $warehouseId);
                })->orWhereHas('destinationLocation', function ($subQ) use ($warehouseId) {
                    $subQ->where('warehouse_id', $warehouseId);
                });
            });
        }

        return $query->with(['product', 'sourceLocation', 'destinationLocation', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
