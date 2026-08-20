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
            'Cây ăn quả nhiệt đới',
            'Cây có múi',
            'Cây leo giàn',
            'Cây bóng mát',
            'Cây độc lạ dễ trồng'
        ];

        foreach ($categories as $index => $cat) {
            Category::create([
                'name' => $cat,
                'slug' => Str::slug($cat),
                'description' => 'Chuyên cung cấp các loại giống ' . mb_strtolower($cat) . ' chất lượng cao, sạch bệnh.',
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }
    }
}