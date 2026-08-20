<?php

namespace App\Http\Requests\Admin;

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
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:products,slug'],
            'sku' => ['nullable', 'string', 'unique:products,sku'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'care_instructions' => ['nullable', 'string'],
            'planting_season' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'lt:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['boolean'],
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'thumbnail.required' => 'Vui lòng tải lên ảnh đại diện.',
        ];
    }
}