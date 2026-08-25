<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'description',
        'unit',
        'status',
        'low_stock_threshold',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
