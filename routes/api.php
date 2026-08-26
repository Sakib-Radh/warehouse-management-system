<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // For Admin & Operator
    Route::middleware('role:admin,warehouse_operator')->group(function () {
        Route::apiResource('products', ProductController::class)->only(['index', 'show']);
        Route::apiResource('warehouses', WarehouseController::class)->only(['index', 'show']);
        Route::apiResource('locations', LocationController::class)->only(['index', 'show']);
        
        Route::get('/inventory', [InventoryController::class, 'index']);
        
        Route::post('/stock-movements', [StockMovementController::class, 'store']);
    });

    // Only for admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('warehouses', WarehouseController::class)->except(['index', 'show']);
        Route::apiResource('locations', LocationController::class)->except(['index', 'show']);
    });
});
