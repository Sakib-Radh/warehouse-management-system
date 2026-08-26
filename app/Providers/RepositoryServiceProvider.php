<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\WarehouseRepository;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\LocationRepository;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Repositories\InventoryRepository;
use App\Repositories\Interfaces\StockMovementRepositoryInterface;
use App\Repositories\StockMovementRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(StockMovementRepositoryInterface::class, StockMovementRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
