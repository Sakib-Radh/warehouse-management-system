<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockMovementRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get filtered stock movements.
     */
    public function getFiltered(array $filters, int $perPage = 15): LengthAwarePaginator;
}
