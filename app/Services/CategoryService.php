<?php

namespace App\Services;

use App\Models\Category;
use App\Exceptions\Category\CategoryCreationException;
use App\Exceptions\Category\DuplicateSlugException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {

            // Crear slug automático
            if (!isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name'] ?? 'default-name', '-');
            }

            // Verificar si el slug ya existe
            if (Category::where('slug', $data['slug'])->exists()) {
                throw new DuplicateSlugException($data['slug']);
            }

            // Asignar un order automáticamente
            if (!isset($data['order'])) {
                $data['order'] = (Category::max('order') ?? 0) + 1;
            }

            $category = Category::create($data);

            return $category;
        });
    }

    /**
     * Actualiza una categoría existente
     */
    public function update(Category $category, array $data): Category
    {
        DB::transaction(function () use ($category, $data) {
            // Actualizar slug si cambió el nombre
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name'], '-');
            }

            // Verificar slug único (excluyendo la categoría actual)
            if (isset($data['slug']) &&
                Category::where('slug', $data['slug'])
                    ->where('id', '!=', $category->id)
                    ->exists()) {
                throw new DuplicateSlugException($data['slug']);
            }

            $category->update($data);
        });

        return $category->fresh();
    }
    /**
     * Elimina una categoría
     */
    public function delete(Category $category): bool
    {
        return DB::transaction(function () use ($category) {

            // Verificar si tiene productos asociados
            if ($category->products()->exists()) {
                throw new CategoryCreationException(
                    "No se puede eliminar: tiene productos asociados",
                    400
                );
            }

            return $category->delete();
        });
    }

    /**
     * Obtiene todas las categorías con paginación y filtros
     */
    public function getAll(array $filters = [])
    {
        $query = Category::query();

        // Filtros
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Ordenamiento
        $sortBy = $filters['sort_by'] ?? 'order';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Obtiene todas las categorías sin paginación (para selects, etc.)
     */
    public function getAllWithoutPagination(array $filters = [])
    {
        $query = Category::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('order')->get();
    }

    /**
     * Busca una categoría por ID
     */
    public function findById(int $id): Category
    {
        return Category::findOrFail($id);
    }
}
