<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ai cũng có quyền lọc và tìm kiếm sản phẩm
    }

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'array'],
            'category_id.*' => ['integer', 'exists:categories,id'],
            'season' => ['nullable', 'string', 'in:spring,summer,autumn,winter,all_year'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'size' => ['nullable', 'string', 'max:20'],
            'brand' => ['nullable', 'string', 'max:100'],
            'tree_age' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'string', 'in:price_asc,price_desc,newest,best_selling'],
        ];
    }
}