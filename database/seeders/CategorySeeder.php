<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Giày Sneaker & Thể Thao',
                'description' => 'Các dòng giày sneaker năng động, phong cách đường phố streetwear và thể thao cá tính.',
            ],
            [
                'name' => 'Giày Chạy Bộ (Running)',
                'description' => 'Giày chạy bộ chuyên nghiệp, siêu nhẹ, đệm khí êm ái hỗ trợ tối đa mọi bước chạy.',
            ],
            [
                'name' => 'Giày Tây & Oxford Công Sở',
                'description' => 'Giày tây cao cấp, da bò tự nhiên 100%, thiết kế lịch lãm, sang trọng cho quý ông.',
            ],
            [
                'name' => 'Giày Lười (Loafer & Slip-on)',
                'description' => 'Giày lười tiện lợi, êm chân, phù hợp đi làm, đi chơi và phong cách smart-casual hiện đại.',
            ],
            [
                'name' => 'Giày Cổ Cao & Boots',
                'description' => 'Giày boot da, chelsea boot và sneaker high-top phong trần, tôn dáng và cá tính.',
            ],
            [
                'name' => 'Dép & Sandal Thời Trang',
                'description' => 'Dép slide thể thao, sandal quai chéo êm nhẹ, phục hồi chân sau vận động.',
            ],
        ];

        foreach ($categories as $index => $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }
    }
}