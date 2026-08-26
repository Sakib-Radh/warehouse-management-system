<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WarehouseService extends BaseService
{
    protected WarehouseRepositoryInterface $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function getAllWarehouses(int $perPage = 15): LengthAwarePaginator
    {
        return $this->warehouseRepository->paginate($perPage);
    }

    public function getWarehouse(int $id): ?Model
    {
        return $this->warehouseRepository->find($id);
    }

    public function createWarehouse(array $data): Model
    {
        return $this->warehouseRepository->create($data);
    }

    public function updateWarehouse(int $id, array $data): bool
    {
        return $this->warehouseRepository->update($id, $data);
    }

    public function deleteWarehouse(int $id): bool
    {
        return $this->warehouseRepository->delete($id);
    }
}
