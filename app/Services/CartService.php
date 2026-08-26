<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_KEY = 'cart';

    // Lấy giỏ hàng từ Session
    public function getSessionCart(): array
    {
        return Session::get(self::CART_KEY, []);
    }

    // Lưu giỏ hàng vào Session
    private function saveToSession(array $cart): void
    {
        Session::put(self::CART_KEY, $cart);
    }

    // Thêm sản phẩm
    public function add(Product $product, int $quantity): array
    {
        $cart = $this->getSessionCart();
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            $cart[$productId]['quantity'] = min($newQuantity, $product->stock_quantity); // Không vượt quá tồn kho
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'slug' => $product->slug,
                'thumbnail' => $product->thumbnail,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'quantity' => min($quantity, $product->stock_quantity),
                'stock_quantity' => $product->stock_quantity,
            ];
        }

        $this->saveToSession($cart);
        return ['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!'];
    }

    // Cập nhật số lượng
    public function update(int $productId, int $quantity): void
    {
        $cart = $this->getSessionCart();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = min($quantity, $cart[$productId]['stock_quantity']);
            }
            $this->saveToSession($cart);
        }
    }

    // Xoá 1 sản phẩm
    public function remove(int $productId): void
    {
        $cart = $this->getSessionCart();
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveToSession($cart);
        }
    }

    // Xoá toàn bộ
    public function clear(): void
    {
        Session::forget(self::CART_KEY);
    }

    // Tính tổng số lượng (cho Badge Navbar)
    public function getTotalQuantity(): int
    {
        $cart = $this->getSessionCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    // Tính tổng tiền (Subtotal)
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
}