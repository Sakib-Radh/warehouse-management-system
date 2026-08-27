<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->operator = User::factory()->create(['role' => 'warehouse_operator']);
    }

    public function test_admin_can_create_product()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'sku' => 'TEST-SKU-001',
            'name' => 'Test Product',
            'description' => 'A product for testing',
            'unit' => 'kg',
            'low_stock_threshold' => 15
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.sku', 'TEST-SKU-001');

        $this->assertDatabaseHas('products', [
            'sku' => 'TEST-SKU-001',
            'unit' => 'kg'
        ]);
    }

    public function test_operator_cannot_create_product()
    {
        $response = $this->actingAs($this->operator)->postJson('/api/products', [
            'sku' => 'TEST-SKU-002',
            'name' => 'Test Product 2',
        ]);

        $response->assertStatus(403);
    }

    public function test_sku_must_be_unique()
    {
        Product::create([
            'sku' => 'UNIQUE-SKU',
            'name' => 'Existing Product',
        ]);

        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'sku' => 'UNIQUE-SKU',
            'name' => 'New Product',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['sku']);
    }

    public function test_can_list_products()
    {
        Product::create(['sku' => 'SKU-1', 'name' => 'Product 1']);
        Product::create(['sku' => 'SKU-2', 'name' => 'Product 2']);

        $response = $this->actingAs($this->operator)->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }
}
 