<?php

namespace Tests\Unit;

use App\Jobs\CheckLowStockAlertJob;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_job_triggers_warning_when_stock_is_below_threshold()
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Low stock alert for Product SKU: LOW-SKU');
            });

        $product = Product::create([
            'sku' => 'LOW-SKU',
            'name' => 'Low Stock Product',
            'low_stock_threshold' => 20,
        ]);

        $warehouse = Warehouse::create(['code' => 'W1', 'name' => 'Warehouse 1']);
        $location = Location::create(['code' => 'L1', 'name' => 'Location 1', 'warehouse_id' => $warehouse->id]);

        Inventory::create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 15,
        ]);

        $job = new CheckLowStockAlertJob($product->id);
        $job->handle();
    }

    public function test_low_stock_job_does_not_trigger_warning_when_stock_is_sufficient()
    {
        Log::shouldReceive('warning')->never();

        $product = Product::create([
            'sku' => 'SUF-SKU',
            'name' => 'Sufficient Stock Product',
            'low_stock_threshold' => 10,
        ]);

        $warehouse = Warehouse::create(['code' => 'W1', 'name' => 'Warehouse 1']);
        $location = Location::create(['code' => 'L1', 'name' => 'Location 1', 'warehouse_id' => $warehouse->id]);

        Inventory::create([
            'product_id' => $product->id,
            'location_id' => $location->id,
            'quantity' => 15,
        ]);

        $job = new CheckLowStockAlertJob($product->id);
        $job->handle();
    }
}
