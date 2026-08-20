<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'short_description', 'description', 
        'care_instructions', 'planting_season', 'fruit_harvest_time', 'tree_age', 
        'tree_height_cm', 'origin', 'price', 'sale_price', 'stock_quantity', 
        'sold_count', 'views_count', 'thumbnail', 'weight_gram', 'status', 
        'is_featured', 'meta_title', 'meta_description'
    ];

    // ==========================================
    // 1. CASTS & ACCESSORS
    // ==========================================

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function getFinalPriceAttribute(): float
    {
        return ($this->sale_price && $this->sale_price < $this->price) 
            ? $this->sale_price 
            : $this->price;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ==========================================
    // 2. RELATIONSHIPS
    // ==========================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // ==========================================
    // 3. QUERY SCOPES (Phục vụ bộ lọc Frontend)
    // ==========================================

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) return $query;
        return $query->whereFullText(['name', 'description'], $keyword)
            ->orWhere('name', 'like', "%{$keyword}%");
    }

    public function scopeInCategory(Builder $query, array|int|null $categoryIds): Builder
    {
        if (empty($categoryIds)) return $query;
        return $query->whereIn('category_id', (array) $categoryIds);
    }

    public function scopeBySeason(Builder $query, ?string $season): Builder
    {
        if (!$season) return $query;
        return $query->where('planting_season', $season);
    }

    public function scopeByPriceRange(Builder $query, ?float $min, ?float $max): Builder
    {
        return $query->where(function ($q) use ($min, $max) {
            $q->when($min !== null, fn($q) => $q->where(DB::raw('COALESCE(sale_price, price)'), '>=', $min))
              ->when($max !== null, fn($q) => $q->where(DB::raw('COALESCE(sale_price, price)'), '<=', $max));
        });
    }

    public function scopeByTreeAge(Builder $query, ?string $treeAge): Builder
    {
        if (!$treeAge) return $query;
        return $query->where('tree_age', $treeAge);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeSortBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc'  => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'best_selling' => $query->orderByDesc('sold_count'),
            default => $query->orderByDesc('created_at'),
        };
    }
}