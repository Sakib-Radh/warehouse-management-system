<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckLowStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $productId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product = Product::find($this->productId);

        if (!$product) {
            return;
        }

        $totalStock = (int) Inventory::where('product_id', $this->productId)->sum('quantity');

        if ($totalStock < $product->low_stock_threshold) {
            Log::warning("Low stock alert for Product SKU: {$product->sku}. Current total stock: {$totalStock}, Threshold: {$product->low_stock_threshold}");
        }
    }
}
