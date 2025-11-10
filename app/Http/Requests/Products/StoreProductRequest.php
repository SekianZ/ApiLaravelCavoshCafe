<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'is_available' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'preparation_time' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_available' => $this->input('is_available', true),
            'is_featured' => $this->input('is_featured', false),
        ]);
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'name.required' => 'El nombre del producto es obligatorio',
            'base_price.required' => 'El precio es obligatorio',
            'base_price.min' => 'El precio no puede ser negativo',
            'base_price.max' => 'El precio no puede superar los 99,999.99',
        ];
    }
}
