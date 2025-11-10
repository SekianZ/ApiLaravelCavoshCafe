<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => $request->query('is_active'),
            'search' => $request->query('search'),
            'sort_by' => $request->query('sort_by', 'order'),
            'sort_order' => $request->query('sort_order', 'asc'),
            'per_page' => $request->query('per_page', 15),
        ];

        $categories = $this->categoryService->getAll($filters);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'from' => $categories->firstItem(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'to' => $categories->lastItem(),
                'total' => $categories->total(),
            ],
            'links' => [
                'first' => $categories->url(1),
                'last' => $categories->url($categories->lastPage()),
                'prev' => $categories->previousPageUrl(),
                'next' => $categories->nextPageUrl(),
            ],
            'message' => 'Categorías obtenidas exitosamente'
        ], 200);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Categoría creada exitosamente'
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'message' => 'Categoría obtenida exitosamente'
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $updatedCategory = $this->categoryService->update($category, $request->validated());

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($updatedCategory),
            'message' => 'Categoría actualizada exitosamente'
        ], 200);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ], 200);
    }
}
