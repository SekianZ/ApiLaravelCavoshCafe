<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'base_price' => $this->base_price,
            'formatted_price' => 'S/. ' . number_format($this->base_price, 2),
            'is_available' => $this->is_available,
            'is_featured' => $this->is_featured,
            'preparation_time' => $this->preparation_time,

            // Relación con categoría
            'category' => $this->when(
                $this->relationLoaded('categories'),
                fn() => [
                    'id' => $this->categories->id,
                    'name' => $this->categories->name,
                    'slug' => $this->categories->slug,
                ]
            ),

            // Imágenes (si usa Spatie Media Library)
            'image' => $this->when(
                $this->hasMedia('images'),
                fn() => $this->getFirstMediaUrl('images')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
