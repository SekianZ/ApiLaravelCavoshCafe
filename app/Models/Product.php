<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'slug',
        'base_price',
        'is_available',
        'is_featured',
        'preparation_time',
    ];

    protected $casts = [
        'base_price' => 'float',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'preparation_time' => 'integer',
    ];

    protected $appends = ['formatted_price'];

    /**
     * Relación: Un producto pertenece a una categoría
     */
    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Accessor para precio formateado
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'S/. ' . number_format($this->base_price, 2);
    }

    /**
     * Configuración de colecciones de media (imágenes)
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->singleFile();
    }
}
