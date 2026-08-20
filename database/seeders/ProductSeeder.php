<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            return;
        }

        $fruits = [
            'Bưởi Da Xanh', 'Cam Sành Mọng Nước', 'Nho Mẫu Đơn Ninh Thuận', 
            'Sầu Riêng Ri6', 'Mít Thái Siêu Sớm', 'Xoài Cát Hòa Lộc', 
            'Ổi Ruby Ruột Đỏ', 'Chanh Dây Tím', 'Lựu Đỏ Ấn Độ', 'Bơ 034'
        ];

        $seasons = ['spring', 'summer', 'autumn', 'winter', 'all_year'];

        foreach ($fruits as $index => $fruit) {
            $price = rand(80, 300) * 1000; // Giá từ 80k - 300k
            $hasSale = (bool) rand(0, 1);

            Product::create([
                'category_id' => $categories->random()->id,
                'name' => $fruit,
                'slug' => Str::slug($fruit) . '-' . time() . $index,
                'sku' => 'CAY' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'short_description' => 'Giống cây ' . $fruit . ' sinh trưởng mạnh, tỷ lệ sống cao, nhanh ra trái.',
                'description' => 'Chi tiết về giống cây ' . $fruit . '. Khả năng chống chịu sâu bệnh tốt, phù hợp với khí hậu 3 miền. Đảm bảo chuẩn giống F1.',
                'care_instructions' => 'Tưới nước 1-2 lần/ngày vào sáng sớm hoặc chiều mát. Bón phân hữu cơ định kỳ 1 tháng/lần.',
                'planting_season' => $seasons[array_rand($seasons)],
                'tree_age' => rand(3, 12) . ' tháng tuổi',
                'tree_height_cm' => rand(40, 120),
                'origin' => 'Việt Nam',
                'price' => $price,
                'sale_price' => $hasSale ? $price - (rand(10, 30) * 1000) : null,
                'stock_quantity' => rand(20, 150),
                'sold_count' => rand(0, 50),
                'status' => 'published',
                'is_featured' => (bool) rand(0, 1),
            ]);
        }
    }
}