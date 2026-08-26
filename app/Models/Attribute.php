<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    // 1 Nhóm thuộc tính có nhiều Giá trị (Kích thước -> Cây giống, Cây choai...)
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->orderBy('sort_order');
    }

    // Nhóm thuộc tính này được áp dụng cho những Sản phẩm nào
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attributes');
    }
}