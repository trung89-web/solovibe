<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation; // Bổ sung Model này
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getSessionCart();
        $subtotal = $this->cartService->getSubtotal();
        
        return view('frontend.cart.index', compact('cart', 'subtotal'));
    }

    // Sửa hàm Add: Không bắt buộc Product trên URL nữa, mà nhận ID từ Request (Form)
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Nếu có gửi lên variation_id thì tìm Variation, không thì để null
        $variation = null;
        if ($request->filled('variation_id')) {
            $variation = ProductVariation::with('attributeValues')->findOrFail($request->variation_id);
        }

        $result = $this->cartService->add($product, $request->quantity, $variation);
        return back()->with('success', $result['message']);
    }

    public function update(Request $request, $cartKey)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $this->cartService->update($cartKey, $request->quantity);
        return back()->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function remove($cartKey)
    {
        $this->cartService->remove($cartKey);
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear()
    {
        $this->cartService->clear();
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}