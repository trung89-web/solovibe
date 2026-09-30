<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultAttributes = [
            'Kích cỡ (Size)' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
            'Màu sắc' => ['Trắng (White)', 'Đen (Black)', 'Xám (Grey)', 'Xanh Navy', 'Đỏ (Red)', 'Phối màu'],
            'Chất liệu' => ['Da thật cao cấp', 'Vải dệt Flyknit', 'Da lộn (Suede)', 'Vải Canvas chịu lực']
        ];

        $sortAttr = 0;
        foreach ($defaultAttributes as $name => $values) {
            $attribute = Attribute::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $sortAttr++
            ]);

            $sortVal = 0;
            foreach ($values as $val) {
                $attribute->values()->create([
                    'value' => $val,
                    'sort_order' => $sortVal++
                ]);
            }
        }
    }
}