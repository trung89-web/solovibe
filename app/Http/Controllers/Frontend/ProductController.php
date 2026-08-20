<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\Frontend\ProductFilterRequest;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm kèm bộ lọc
     */
    public function index(ProductFilterRequest $request)
    {
        // Lấy các tham số lọc đã qua validation
        $filters = $request->validated();

        // Build query bằng các local scopes đã tạo ở Task 3
        $products = Product::published()
            ->search($filters['keyword'] ?? null)
            ->inCategory($filters['category_id'] ?? null)
            ->bySeason($filters['season'] ?? null)
            ->byPriceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->byTreeAge($filters['tree_age'] ?? null)
            ->sortBy($filters['sort'] ?? 'newest')
            ->paginate(12)
            ->withQueryString(); // Giữ nguyên các tham số trên URL khi chuyển trang

        // Load danh mục để hiển thị ở Sidebar (chỉ lấy danh mục cha và các danh mục con đang active)
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();

        return view('frontend.products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show(Product $product)
    {
        // Kiểm tra bảo mật: Chỉ cho phép xem nếu sản phẩm đang được publish
        abort_if($product->status !== 'published', 404);

        // Tăng lượt xem lên 1
        $product->increment('views_count');
        
        // Eager load các quan hệ cần thiết
        $product->load(['images', 'category']);

        // Lấy 4 sản phẩm liên quan (cùng danh mục, trừ sản phẩm hiện tại)
        $relatedProducts = Product::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }
}