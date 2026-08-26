<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 
        'stock_quantity', 'image', 'is_default', 'status'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'is_default' => 'boolean',
            'status' => 'boolean',
        ];
    }

    // Biến thể này thuộc về Sản phẩm gốc nào
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Biến thể này là sự kết hợp của những Giá trị thuộc tính nào
    // (VD: Cây trưởng thành + Ghép mắt + Bầu to)
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'variation_attribute_values');
    }
}