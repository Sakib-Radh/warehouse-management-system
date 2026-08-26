<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class InventoryService extends BaseService
{
    protected InventoryRepositoryInterface $inventoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Get paginated inventory data and cache the result.
     */
    public function getInventory(array $filters, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $cacheVersion = Cache::rememberForever('inventory_cache_version', function () {
            return 1;
        });

        $cacheKey = 'inventory_v' . $cacheVersion . '_' . md5(json_encode($filters) . '_' . $perPage . '_' . $page);

        return Cache::remember($cacheKey, 3600, function () use ($filters, $perPage) {
            return $this->inventoryRepository->getFiltered($filters, $perPage);
        });
    }

    /**
     * Invalidate the inventory cache by bumping the version.
     */
    public function invalidateCache(): void
    {
        Cache::increment('inventory_cache_version');
    }
}
