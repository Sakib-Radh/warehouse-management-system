# Warehouse Management System (WMS) Backend

This repository contains the backend for the Warehouse Management System, built with Laravel 11. It provides a robust set of RESTful APIs for managing products, warehouses, locations, and real-time inventory tracking.

## Key Features
- **Products, Warehouses, & Locations CRUD**: Complete management APIs for core domain entities.
- **Stock Movements**: Handles `receive`, `dispatch`, and `transfer` operations accurately.
- **Concurrency & Race Condition Handling**: Uses PostgreSQL pessimistic locking (`lockForUpdate()`) within Database Transactions to prevent negative inventory when multiple dispatch operations occur simultaneously.
- **High-Performance Caching**: Redis is integrated into the Inventory Read API (`GET /api/inventory`), caching paginated results to reduce database load. The cache is invalidated automatically upon any new stock movement.
- **Background Processing**: A Redis-backed queue asynchronously processes low-stock alerts during dispatch/transfer operations, preventing slow response times for the end-user.
- **Role-Based Access**: Distinguishes between `admin` (full access) and `warehouse_operator` (read-only for entities, but can perform stock movements).

## Requirements
- PHP 8.2+
- Composer
- Docker (for Laravel Sail)

## Setup Instructions

1. **Clone and Install Dependencies**
   ```bash
   git clone <repository-url>
   cd warehouse-management-system
   composer install
   ```

2. **Environment Configuration**
   Copy the example `.env` file and ensure your database and Redis configurations are correct.
   ```bash
   cp .env.example .env
   ```
   *Make sure `QUEUE_CONNECTION=redis` and `CACHE_STORE=redis` are set in your `.env`.*

3. **Start Laravel Sail (Docker)**
   This project uses Laravel Sail to spin up PostgreSQL and Redis containers.
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Run Migrations & Generate Key**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate
   ```

5. **Start the Queue Worker**
   To process the asynchronous low-stock alerts, start the queue worker in a separate terminal:
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

## Running Tests
Automated tests are included for CRUD operations, stock movement logic, and the low-stock alert background jobs.
```bash
./vendor/bin/sail artisan test
```

## Concurrency Design Decisions

Handling concurrent stock dispatches is a critical requirement in a WMS to prevent negative inventory. This system solves race conditions using **Pessimistic Locking**:

When a `dispatch` or `transfer` occurs, the `StockMovementService` wraps the operation in a `DB::transaction()`. It queries the `Inventory` model using `->lockForUpdate()`. 

```php
$inventory = Inventory::where('product_id', $productId)
    ->where('location_id', $locationId)
    ->lockForUpdate()
    ->first();
```

If User A and User B attempt to dispatch the same product simultaneously, PostgreSQL will lock that specific inventory row for User A. User B's request will block and wait until User A's transaction commits. Once User A finishes, User B reads the *newly updated* quantity, and if it falls below zero, an Exception is safely thrown, completely preventing negative stock.
