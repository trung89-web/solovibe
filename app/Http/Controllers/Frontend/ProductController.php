<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\Frontend\ProductFilterRequest;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm kèm bộ lọc
     */
    public function index(ProductFilterRequest $request)
    {
        // Lấy các tham số lọc đã qua validation
        $filters = $request->validated();

        // Build query bằng các local scopes
        $products = Product::published()
            ->search($filters['keyword'] ?? null)
            ->inCategory($filters['category_id'] ?? null)
            ->bySeason($filters['season'] ?? null)
            ->byPriceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->bySize($filters['size'] ?? null)
            ->byBrand($filters['brand'] ?? null)
            ->byTreeAge($filters['tree_age'] ?? null)
            ->sortBy($filters['sort'] ?? 'newest')
            ->paginate(12)
            ->withQueryString(); // Giữ nguyên các tham số trên URL khi chuyển trang

        // Load danh mục để hiển thị ở Sidebar
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();

        // Load danh sách Size để hiển thị trên bộ lọc
        $sizeAttr = \App\Models\Attribute::where('name', 'like', '%Size%')->first();
        $availableSizes = $sizeAttr ? $sizeAttr->values : collect();

        return view('frontend.products.index', compact('products', 'categories', 'availableSizes'));
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show(string $slug)
    {
        // 1. Tải Sản phẩm kèm theo Biến thể và Nhóm thuộc tính
        $product = \App\Models\Product::with([
            'images', 
            'category', 
            'attributes.values', 
            'variations.attributeValues',
        ])->where('slug', $slug)->firstOrFail();

        if (Schema::hasTable('product_reviews')) {
            $product->load('reviews.user');
        } else {
            $product->setRelation('reviews', collect());
        }

        // 2. Tăng lượt xem
        $product->increment('views_count');

        // 3. Lấy sản phẩm liên quan
        $relatedProducts = \App\Models\Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->published()
            ->limit(4)
            ->get();

        // 4. Đóng gói dữ liệu Biến thể ra JSON để gửi xuống JavaScript
        $variationsJson = $product->variations->map(function ($var) {
            return [
                'id' => $var->id,
                'price' => $var->price,
                'sale_price' => $var->sale_price,
                'stock_quantity' => $var->stock_quantity,
                // Lấy mảng ID các giá trị và sắp xếp tăng dần để dễ so sánh ở JS
                'attribute_value_ids' => $var->attributeValues->pluck('id')->sort()->values()->toArray(),
            ];
        });

        return view('frontend.products.show', compact('product', 'relatedProducts', 'variationsJson'));
    }
}