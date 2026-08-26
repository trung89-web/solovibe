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
            'Kích thước / Độ tuổi' => ['Cây giống', 'Cây choai', 'Cây trưởng thành'],
            'Phương pháp nhân giống' => ['Gieo hạt', 'Chiết cành', 'Ghép mắt'],
            'Tình trạng đóng gói' => ['Rễ trần', 'Bầu nhỏ', 'Vô chậu / Bầu to']
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