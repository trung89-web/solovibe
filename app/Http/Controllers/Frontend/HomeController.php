<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Danh mục nổi bật
        $featuredCategories = Category::where('is_active', true)->orderBy('id')->take(8)->get();

        // 2. Sản phẩm nổi bật
        $featuredProducts = Product::with('category')->where('is_featured', true)->where('status', 'published')->latest()->take(8)->get();

        // 3. Sản phẩm mới nhất
        $latestProducts = Product::with('category')->where('status', 'published')->latest()->take(8)->get();

        // 4. Sản phẩm giảm giá
        $discountProducts = Product::with('category')
            ->where('status', 'published')
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->take(8)->get();

        return view('welcome', compact('featuredCategories', 'featuredProducts', 'latestProducts', 'discountProducts'));
    }
}