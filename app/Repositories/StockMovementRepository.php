<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\StockMovement;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;

class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    public function __construct(StockMovement $model)
    {
        parent::__construct($model);
    }
}
