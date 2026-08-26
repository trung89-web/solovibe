<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value', 'sort_order'];

    // Giá trị này thuộc về Nhóm thuộc tính nào
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    // Giá trị này đang nằm trong những Biến thể (SKU) nào
    public function variations(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariation::class, 'variation_attribute_values');
    }
}