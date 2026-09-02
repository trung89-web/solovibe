<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation; // Bổ sung
use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_KEY = 'cart';

    public function getSessionCart(): array
    {
        return Session::get(self::CART_KEY, []);
    }

    private function saveToSession(array $cart): void
    {
        Session::put(self::CART_KEY, $cart);
    }

    // Nâng cấp hàm Add: Nhận thêm biến $variation (Có thể null)
    public function add(Product $product, int $quantity, ?ProductVariation $variation = null): array
    {
        $cart = $this->getSessionCart();
        
        // Tạo Cart Key duy nhất (VD: V_12 nếu là Biến thể, P_5 nếu là Sản phẩm thường)
        $cartKey = $variation ? 'V_' . $variation->id : 'P_' . $product->id;

        // Quyết định Giá và Tồn kho dựa trên việc mua SP thường hay mua Biến thể
        $price = $variation ? $variation->price : $product->price;
        $salePrice = $variation ? $variation->sale_price : $product->sale_price;
        $stockLimit = $variation ? $variation->stock_quantity : $product->stock_quantity;
        
        // Lấy tên các thuộc tính ghép lại (VD: "Cây giống - Ghép mắt")
        $variationLabel = '';
        if ($variation && $variation->attributeValues->count() > 0) {
            $variationLabel = $variation->attributeValues->pluck('value')->implode(' - ');
        }

        if (isset($cart[$cartKey])) {
            $newQuantity = $cart[$cartKey]['quantity'] + $quantity;
            $cart[$cartKey]['quantity'] = min($newQuantity, $stockLimit);
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variation_id' => $variation ? $variation->id : null,
                'name' => $product->name,
                'variation_label' => $variationLabel, // Thêm nhãn Biến thể
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail,
                'price' => $price,
                'sale_price' => $salePrice,
                'quantity' => min($quantity, $stockLimit),
                'stock_quantity' => $stockLimit,
            ];
        }

        $this->saveToSession($cart);
        return ['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!'];
    }

    // Đổi kiểu dữ liệu của ID truyền vào thành string ($cartKey thay vì int $productId)
    public function update(string $cartKey, int $quantity): void
    {
        $cart = $this->getSessionCart();

        if (isset($cart[$cartKey])) {
            if ($quantity <= 0) {
                unset($cart[$cartKey]);
            } else {
                $cart[$cartKey]['quantity'] = min($quantity, $cart[$cartKey]['stock_quantity']);
            }
            $this->saveToSession($cart);
        }
    }

    public function remove(string $cartKey): void
    {
        $cart = $this->getSessionCart();
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            $this->saveToSession($cart);
        }
    }

    public function clear(): void
    {
        Session::forget(self::CART_KEY);
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->getSessionCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    public function getSubtotal(): float
    {
        $cart = $this->getSessionCart();
        $total = 0;
        foreach ($cart as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $total += $price * $item['quantity'];
        }
        return $total;
    }

    public function removeMultiple(array $cartKeys): void
    {
        $cart = $this->getSessionCart();
        foreach ($cartKeys as $key) {
            if (isset($cart[$key])) {
                unset($cart[$key]);
            }
        }
        $this->saveToSession($cart);
    }
}