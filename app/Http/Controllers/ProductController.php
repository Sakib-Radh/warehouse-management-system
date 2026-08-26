<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $products = $this->productService->getAllProducts($perPage);
        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request): ProductResource
    {
        $product = $this->productService->createProduct($request->validated());
        return new ProductResource($product);
    }

    public function show(int $id): ProductResource|JsonResponse
    {
        $product = $this->productService->getProduct($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return new ProductResource($product);
    }

    public function update(ProductRequest $request, int $id): ProductResource|JsonResponse
    {
        $updated = $this->productService->updateProduct($id, $request->validated());

        if (! $updated) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product = $this->productService->getProduct($id);
        return new ProductResource($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);

        if (! $deleted) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(null, 204);
    }
}
