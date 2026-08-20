<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Khai báo các trường cho phép gán dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    /**
     * Override getRouteKeyName để dùng route-model-binding qua 'slug' thay vì 'id'
     *
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Quan hệ danh mục cha (self-referencing)
     *
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Quan hệ danh mục con (self-referencing)
     *[cite: 1]
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}