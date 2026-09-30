<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

it('allows a user to review a product from a completed order item', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Giày chạy bộ',
        'slug' => 'giay-chay-bo',
        'description' => 'Phụ kiện chạy bộ',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'SoleVibe Runner Pro',
        'slug' => 'solevibe-runner-pro',
        'sku' => 'SVR-001',
        'short_description' => 'Giày chạy bộ nhẹ, bền bỉ',
        'description' => 'Mô tả chi tiết',
        'price' => 2500000,
        'sale_price' => 2200000,
        'stock_quantity' => 20,
        'status' => 'published',
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'order_code' => 'SV-REV-001',
        'receiver_name' => 'Người dùng test',
        'receiver_phone' => '0900000001',
        'province' => 'Hà Nội',
        'district' => 'Cầu Giấy',
        'ward' => 'Nghĩa Đô',
        'specific_address' => '123 Đường Test',
        'total_amount' => 2200000,
        'payment_method' => 'cod',
        'payment_status' => 'paid',
        'order_status' => 'completed',
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 2200000,
        'quantity' => 1,
        'subtotal' => 2200000,
    ]);

    $response = $this->actingAs($user)->post('/reviews', [
        'order_item_id' => $orderItem->id,
        'rating' => 5,
        'comment' => 'Sản phẩm rất tốt, giao hàng nhanh và chất lượng vượt mong đợi.',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('product_reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
        'rating' => 5,
    ]);
});

it('prevents duplicate reviews for the same completed order item', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Phụ kiện thể thao',
        'slug' => 'phu-kien-the-thao',
        'description' => 'Phụ kiện',
        'is_active' => true,
        'sort_order' => 2,
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'SoleVibe Socks',
        'slug' => 'solevibe-socks',
        'sku' => 'SVS-010',
        'short_description' => 'Vớ thể thao',
        'description' => 'Mô tả',
        'price' => 120000,
        'stock_quantity' => 10,
        'status' => 'published',
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'order_code' => 'SV-REV-002',
        'receiver_name' => 'Người dùng test',
        'receiver_phone' => '0900000002',
        'province' => 'HCM',
        'district' => 'Quận 1',
        'ward' => 'Bến Nghé',
        'specific_address' => '456 Đường Test',
        'total_amount' => 120000,
        'payment_method' => 'cod',
        'payment_status' => 'paid',
        'order_status' => 'completed',
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => 120000,
        'quantity' => 1,
        'subtotal' => 120000,
    ]);

    $this->actingAs($user)->post('/reviews', [
        'order_item_id' => $orderItem->id,
        'rating' => 4,
        'comment' => 'Đẹp và tiện dụng.',
    ]);

    $response = $this->actingAs($user)->from('/profile')->post('/reviews', [
        'order_item_id' => $orderItem->id,
        'rating' => 5,
        'comment' => 'Đánh giá lần hai',
    ]);

    $response->assertSessionHasErrors(['order_item_id']);
});
