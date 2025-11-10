<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    /**
     * Display a listing of the resource with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'category_id' => $request->query('category_id'),
            'is_available' => $request->query('is_available'),
            'is_featured' => $request->query('is_featured'),
            'search' => $request->query('search'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
            'sort_by' => $request->query('sort_by', 'created_at'),
            'sort_order' => $request->query('sort_order', 'desc'),
            'per_page' => $request->query('per_page', 15),
        ];

        $products = $this->productService->getAll($filters);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ],
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ],
            'message' => 'Productos obtenidos exitosamente'
        ], 200);
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $product = $this->productService->create($validatedData);

        if ($request->hasFile('image')) {
            $this->productService->uploadImage($product, $request->file('image'));
        }

        // Respuesta exitosa con el producto creado
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Producto creado exitosamente'
        ], 201);
    }

    /**
     * Display the specified resource
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Producto obtenido exitosamente'
        ], 200);
    }

    /**
     * Update the specified resource in storage
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = $this->productService->update($product, $request->validated());

        return response()->json([
            'success' => true,
            'data' => new ProductResource($updatedProduct),
            'message' => 'Producto actualizado exitosamente'
        ], 200);
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ], 200);
    }

    /**
     * Get product by slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $product = $this->productService->findBySlug($slug);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
            'message' => 'Producto obtenido exitosamente'
        ], 200);
    }

    /**
     * Upload or update product image
     */
    public function uploadImage(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'], // 2MB max
        ]);

        $this->productService->uploadImage($product, $request->file('image'));

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product->fresh()),
            'message' => 'Imagen subida exitosamente'
        ], 200);
    }

    /**
     * Delete product image
     */
    public function deleteImage(Product $product): JsonResponse
    {
        $this->productService->deleteImage($product);

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada exitosamente'
        ], 200);
    }
}
