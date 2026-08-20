<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', Rule::unique('products', 'sku')->ignore($productId)],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'care_instructions' => ['nullable', 'string'],
            'planting_season' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'lt:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['boolean'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
        ];
    }
}