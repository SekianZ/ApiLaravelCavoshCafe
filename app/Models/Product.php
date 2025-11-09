<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'base_price',
        'is_available',
        'is_featured',
        'visible_online'
    ];

    protected $casts = [
        'base_price' => 'decimal:8,2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'visible_online' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }
}
