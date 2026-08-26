<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    // Inject CartService qua constructor
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getSessionCart();
        $subtotal = $this->cartService->getSubtotal();
        
        // Trả về view (Sẽ tạo ở bước sau)
        return view('frontend.cart.index', compact('cart', 'subtotal'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $result = $this->cartService->add($product, $request->quantity);
        return back()->with('success', $result['message']);
    }

    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $this->cartService->update($productId, $request->quantity);
        return back()->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function remove($productId)
    {
        $this->cartService->remove($productId);
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear()
    {
        $this->cartService->clear();
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}