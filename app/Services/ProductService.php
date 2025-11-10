<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Exceptions\Product\ProductCreationException;
use App\Exceptions\Product\DuplicateSlugException;
use App\Exceptions\Product\InvalidCategoryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Crea un nuevo producto
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            // Verificar que la categoría existe
            if (!Category::where('id', $data['category_id'])->exists()) {
                throw new InvalidCategoryException();
            }

            // Crear slug automático si no viene
            if (!isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name'] ?? 'default-product', '-');
            }

            // Verificar si el slug ya existe
            if (Product::where('slug', $data['slug'])->exists()) {
                throw new DuplicateSlugException($data['slug']);
            }

            $product = Product::create($data);

            return $product;
        });
    }

    /**
     * Actualiza un producto existente
     */
    public function update(Product $product, array $data): Product
    {
        DB::transaction(function () use ($product, $data) {

            // Verificar categoría si se está actualizando
            if (isset($data['category_id']) && !Category::where('id', $data['category_id'])->exists()) {
                throw new InvalidCategoryException();
            }

            // Actualizar slug si cambió el nombre
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name'], '-');
            }

            // Verificar slug único (excluyendo el producto actual)
            if (isset($data['slug']) &&
                Product::where('slug', $data['slug'])
                    ->where('id', '!=', $product->id)
                    ->exists()) {
                throw new DuplicateSlugException($data['slug']);
            }

            $product->update($data);
        });

        $product->refresh();

        return $product;
    }

    /**
     * Elimina un producto
     */
    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Aquí podrías agregar validaciones adicionales
            // Por ejemplo, verificar si tiene pedidos asociados

            return $product->delete();
        });
    }

    /**
     * Obtiene todos los productos con paginación y filtros
     */
    public function getAll(array $filters = [])
    {
        $query = Product::with('categories');

        // Filtro por categoría
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filtro por disponibilidad
        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        // Filtro por destacado
        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        // Búsqueda por nombre o descripción
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filtro por rango de precio
        if (isset($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        // Ordenamiento
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Obtiene todos los productos sin paginación
     */
    public function getAllWithoutPagination(array $filters = [])
    {
        $query = Product::with('categories');

        if (isset($filters['is_available'])) {
            $query->where('is_available', $filters['is_available']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Busca un producto por ID
     */
    public function findById(int $id): Product
    {
        return Product::with('categories')->findOrFail($id);
    }

    /**
     * Busca un producto por slug
     */
    public function findBySlug(string $slug): Product
    {
        $product = Product::with('categories')->where('slug', $slug)->first();

        if (!$product) {
            throw new ProductCreationException("Producto con slug '{$slug}' no encontrado", 404);
        }

        return $product;
    }

    /**
     * Sube o actualiza la imagen del producto
     */
    public function uploadImage(Product $product, $image): Product
    {
        // Si ya tiene una imagen, la elimina primero (singleFile ya lo hace automáticamente)
        $product->addMedia($image)
            ->toMediaCollection('images');

        return $product->fresh();
    }

    /**
     * Elimina la imagen del producto
     */
    public function deleteImage(Product $product): bool
    {
        if ($product->hasMedia('images')) {
            $product->clearMediaCollection('images');
            return true;
        }

        return false;
    }
}
