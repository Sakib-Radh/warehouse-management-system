<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOperationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;
    protected Location $locationA;
    protected Location $locationB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        
        $warehouse = Warehouse::create([
            'code' => 'WH01',
            'name' => 'Main Warehouse'
        ]);

        $this->locationA = Location::create([
            'warehouse_id' => $warehouse->id,
            'code' => 'LOC-A',
            'name' => 'Location A'
        ]);

        $this->locationB = Location::create([
            'warehouse_id' => $warehouse->id,
            'code' => 'LOC-B',
            'name' => 'Location B'
        ]);

        $this->product = Product::create([
            'sku' => 'SKU001',
            'name' => 'Test Product',
            'low_stock_threshold' => 10,
        ]);
    }

    public function test_can_receive_stock()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/stock-movements', [
            'type' => 'receive',
            'product_id' => $this->product->id,
            'quantity' => 50,
            'destination_location_id' => $this->locationA->id,
            'reference_number' => 'REC-001'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('inventories', [
            'product_id' => $this->product->id,
            'location_id' => $this->locationA->id,
            'quantity' => 50
        ]);
    }

    public function test_cannot_dispatch_more_than_available()
    {
        Inventory::create([
            'product_id' => $this->product->id,
            'location_id' => $this->locationA->id,
            'quantity' => 20
        ]);

        $response = $this->actingAs($this->admin)->postJson('/api/stock-movements', [
            'type' => 'dispatch',
            'product_id' => $this->product->id,
            'quantity' => 30,
            'source_location_id' => $this->locationA->id,
            'reference_number' => 'DIS-001'
        ]);

        $response->assertStatus(422);
        
        $this->assertDatabaseHas('inventories', [
            'product_id' => $this->product->id,
            'location_id' => $this->locationA->id,
            'quantity' => 20
        ]);
    }
}
