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

        // Nếu là request từ JS (Fetch/AJAX), trả về JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'cartTotalQuantity' => $this->cartService->getTotalQuantity()
            ]);
        }
        return back()->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function remove(Request $request, $cartKey)
    {
        $this->cartService->remove($cartKey);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm',
                'cartTotalQuantity' => $this->cartService->getTotalQuantity()
            ]);
        }
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear(Request $request)
    {
        $this->cartService->clear();

        // Nếu gọi qua AJAX (Fetch API) từ nút xóa toàn bộ
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng'
            ]);
        }

        // Fallback cho trường hợp load lại form truyền thống
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }

    public function removeMultiple(Request $request)
    {
        $request->validate([
            'keys' => 'required|array'
        ]);

        $this->cartService->removeMultiple($request->keys);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa các sản phẩm được chọn',
                'cartTotalQuantity' => $this->cartService->getTotalQuantity()
            ]);
        }
        return back()->with('success', 'Đã xóa các sản phẩm được chọn!');
    }
}