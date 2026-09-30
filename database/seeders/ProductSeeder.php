<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        if ($categories->isEmpty()) {
            return;
        }

        // Lấy các thuộc tính size và màu
        $sizeAttr = Attribute::where('name', 'like', '%Size%')->first();
        $colorAttr = Attribute::where('name', 'like', '%Màu%')->first();

        $sizeValues = $sizeAttr ? $sizeAttr->values : collect();
        $colorValues = $colorAttr ? $colorAttr->values : collect();

        $shoeProducts = [
            [
                'cat' => 'Giày Sneaker & Thể Thao',
                'name' => 'Nike Air Jordan 1 Retro High OG',
                'sku' => 'NK-AJ1-001',
                'origin' => 'Nike - Chính Hãng',
                'material' => 'Da bò Full-grain cao cấp',
                'sole_height' => 4,
                'warranty' => 'Bảo hành keo chỉ 12 tháng',
                'season' => 'all_year',
                'price' => 4200000,
                'sale_price' => 3850000,
                'short' => 'Huyền thoại bóng rổ và văn hóa đường phố toàn cầu với chất liệu da cao cấp và phối màu kinh điển.',
                'desc' => '<p><strong>Nike Air Jordan 1 Retro High OG</strong> là biểu tượng trường tồn trong lịch sử giày thể thao thế giới. Sở hữu bộ đệm Air-Sole êm ái tại gót chân, thân giày làm từ da thật cao cấp cho độ bền vượt trội và tính thẩm mỹ đỉnh cao.</p><p>Đế cao su với các rãnh uốn sâu mang lại độ bám đường tuyệt đối trên mọi bề mặt sân chơi và đường phố.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['39', '40', '41', '42', '43'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Giày Sneaker & Thể Thao',
                'name' => 'Nike Air Force 1 07 All White',
                'sku' => 'NK-AF1-002',
                'origin' => 'Nike - Chính Hãng',
                'material' => 'Da tổng hợp & Da bò cao cấp',
                'sole_height' => 4,
                'warranty' => 'Bảo hành 12 tháng chính hãng',
                'season' => 'all_year',
                'price' => 2900000,
                'sale_price' => 2650000,
                'short' => 'Đôi giày "quốc dân" màu trắng tinh tế, dễ dàng phối đồ với mọi phong cách từ casual đến smart streetwear.',
                'desc' => '<p>Giữ trọn nét cổ điển của dòng AF1 huyền thoại, đôi <strong>Nike Air Force 1 07</strong> mang tông màu trắng tinh khôi, đệm Nike Air toàn phần mang lại sự êm ái cả ngày dài.</p><p>Thiết kế cổ thấp đệm lót êm ái giúp mắt cá chân luôn thoải mái, phong cách thanh thoát trường tồn cùng thời gian.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Giày Chạy Bộ (Running)',
                'name' => 'Adidas Ultraboost Light 23 Pro',
                'sku' => 'AD-UB23-003',
                'origin' => 'Adidas - Chính Hãng',
                'material' => 'Vải dệt Primeknit+ tái chế',
                'sole_height' => 3,
                'warranty' => 'Bảo hành đế Boost 12 tháng',
                'season' => 'summer',
                'price' => 5200000,
                'sale_price' => 4490000,
                'short' => 'Công nghệ đệm Light BOOST thế hệ mới nhẹ hơn 30%, hoàn trả năng lượng tối đa cho vận động viên chạy cự ly.',
                'desc' => '<p><strong>Adidas Ultraboost Light</strong> là phiên bản giày chạy bộ nhẹ nhất từ trước đến nay của Adidas. Hệ thống đệm Light BOOST cung cấp khả năng đàn hồi và lực đẩy vượt bậc, giảm thiểu mệt mỏi cho đôi chân.</p><p>Đế ngoài Continental™ Better Rubber đảm bảo độ bám hoàn hảo cả trên đường khô ráo lẫn ướt trơn trượt.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['39', '40', '41', '42', '43'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Giày Sneaker & Thể Thao',
                'name' => 'Adidas Samba OG Classic Vintage',
                'sku' => 'AD-SAMBA-004',
                'origin' => 'Adidas - Chính Hãng',
                'material' => 'Da mềm cao cấp kết hợp mũi da lộn',
                'sole_height' => 2,
                'warranty' => 'Bảo hành chính hãng 12 tháng',
                'season' => 'all_year',
                'price' => 2800000,
                'sale_price' => null,
                'short' => 'Cơn sốt thời trang retro toàn cầu, thiết kế đế cao su gum kinh điển không thể thiếu trong tủ đồ giới trẻ.',
                'desc' => '<p>Sinh ra trên sân cỏ bóng đá thập niên 50 và trở thành biểu tượng thời trang đường phố đương đại, <strong>Adidas Samba OG</strong> mang phong cách tối giản với 3 sọc tương phản đặc trưng cùng mũi chữ T bằng da lộn cao cấp.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['37', '38', '39', '40', '41', '42'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Giày Sneaker & Thể Thao',
                'name' => 'Vans Old Skool Classic Core Black/White',
                'sku' => 'VN-OS-005',
                'origin' => 'Vans - Off The Wall',
                'material' => 'Vải Canvas 12oz & Da lộn Suede',
                'sole_height' => 3,
                'warranty' => 'Bảo hành keo chỉ 6 tháng',
                'season' => 'all_year',
                'price' => 1950000,
                'sale_price' => 1750000,
                'short' => 'Dải sọc Jazz stripe huyền thoại của văn hóa trượt ván California, bền bỉ và cá tính vượt thời gian.',
                'desc' => '<p><strong>Vans Old Skool</strong> là mẫu giày trượt ván kinh điển đầu tiên mang biểu tượng sọc viền đặc trưng. Thân giày phối da lộn và vải bạt bền chắc, đầu mũi giày được gia cố chống mài mòn cao.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42'],
                'is_featured' => false,
            ],
            [
                'cat' => 'Giày Sneaker & Thể Thao',
                'name' => 'Converse Chuck 70 Vintage Canvas',
                'sku' => 'CV-C70-006',
                'origin' => 'Converse - Chính Hãng',
                'material' => 'Vải Canvas cao cấp 14oz',
                'sole_height' => 3,
                'warranty' => 'Bảo hành 6 tháng',
                'season' => 'all_year',
                'price' => 2200000,
                'sale_price' => 1990000,
                'short' => 'Đế cao su tráng men bóng cổ điển, lót đệm OrthoLite êm ái, đường kim mũi chỉ vintage sắc sảo.',
                'desc' => '<p><strong>Converse Chuck 70</strong> tôn vinh di sản thiết kế nguyên bản thập niên 70 với chất liệu vải canvas cao cấp dày dặn, viền đế cao su ngà bóng bẩy và lớp đệm lót công nghệ mới mang lại cảm giác êm chân suốt ngày dài.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['37', '38', '39', '40', '41', '42', '43'],
                'is_featured' => false,
            ],
            [
                'cat' => 'Giày Tây & Oxford Công Sở',
                'name' => 'Giày Tây Oxford Captoe Da Bò Ý Thủ Công',
                'sku' => 'OX-IT-007',
                'origin' => 'Việt Nam Xuất Khẩu (Chất liệu da Ý)',
                'material' => 'Da bò hạt Mill Ý tự nhiên 100%',
                'sole_height' => 3,
                'warranty' => 'Bảo hành da và đế 24 tháng',
                'season' => 'all_year',
                'price' => 2450000,
                'sale_price' => 2190000,
                'short' => 'Đường nét Oxford đóng kín lịch lãm, phom dáng chuẩn quý ông công sở, hoàn thiện tỉ mỉ bằng tay.',
                'desc' => '<p>Đôi giày tây <strong>Oxford Captoe</strong> là chuẩn mực của sự trang trọng. Làm từ da bò cao cấp nhập khẩu từ Ý, xử lý chống nhăn và chống thấm nước nhẹ. Đế cao su phíp cao cấp chống trơn trượt và êm ái cho những ngày làm việc dài.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['39', '40', '41', '42', '43'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Giày Lười (Loafer & Slip-on)',
                'name' => 'Penny Loafer Da Thật Smart Casual',
                'sku' => 'LF-PN-008',
                'origin' => 'Việt Nam Xuất Khẩu',
                'material' => 'Da bò mộc lót da cừu êm chân',
                'sole_height' => 3,
                'warranty' => 'Bảo hành 12 tháng keo chỉ',
                'season' => 'all_year',
                'price' => 1850000,
                'sale_price' => 1590000,
                'short' => 'Dáng giày lười penny thanh lịch, tiện dụng xỏ chân nhanh chóng, phù hợp phối quần âu, kaki và jeans.',
                'desc' => '<p><strong>Penny Loafer</strong> là sự kết hợp hoàn hảo giữa phong thái lịch thiệp công sở và sự thoải mái năng động cuối tuần. Đệm lót da cừu mềm mại chống đau gót chân, nâng đỡ vòm bàn chân hoàn hảo.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['39', '40', '41', '42', '43'],
                'is_featured' => false,
            ],
            [
                'cat' => 'Giày Cổ Cao & Boots',
                'name' => 'Chelsea Boots Da Lộn Phong Trần (Suede)',
                'sku' => 'BT-CH-009',
                'origin' => 'Việt Nam Thiết Kế Cao Cấp',
                'material' => 'Da lộn bò nhập khẩu cao cấp',
                'sole_height' => 4,
                'warranty' => 'Bảo hành 12 tháng',
                'season' => 'autumn',
                'price' => 2600000,
                'sale_price' => 2290000,
                'short' => 'Thiết kế cổ chun đàn hồi ôm chân gọn gàng, tăng chiều cao tinh tế 4cm, tôn dáng cực đỉnh.',
                'desc' => '<p>Mẫu <strong>Chelsea Boots Suede</strong> mang đậm phong cách phóng khoáng kiểu Anh. Lớp da lộn được xử lý công nghệ nano kháng bẩn nhẹ, đế cao su đúc nguyên khối chống mòn hiệu quả.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['39', '40', '41', '42', '43'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Dép & Sandal Thời Trang',
                'name' => 'Dép Slide Quai Ngang Recovery Sport',
                'sku' => 'SL-SP-010',
                'origin' => 'Hàn Quốc',
                'material' => 'Đệm bọt khí EVA nguyên khối đúc nhiệt',
                'sole_height' => 4,
                'warranty' => 'Bảo hành 6 tháng',
                'season' => 'summer',
                'price' => 650000,
                'sale_price' => 490000,
                'short' => 'Đế bánh mì công thái học đàn hồi siêu êm, giảm áp lực cơ chân sau khi chạy bộ và tập thể thao.',
                'desc' => '<p><strong>Dép Slide Recovery Sport</strong> mang đến trải nghiệm êm ái như bước đi trên mây nhờ chất liệu bọt khí EVA cao cấp không thấm nước, nhanh khô và cực kỳ nhẹ nhàng.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'is_featured' => false,
            ],
            [
                'cat' => 'Giày Sneaker & Thể Thao',
                'name' => 'New Balance 550 White Grey Retro',
                'sku' => 'NB-550-011',
                'origin' => 'New Balance - USA',
                'material' => 'Da hạt cao cấp kết hợp vải lưới thoáng khí',
                'sole_height' => 3,
                'warranty' => 'Bảo hành 12 tháng chính hãng',
                'season' => 'all_year',
                'price' => 3600000,
                'sale_price' => 3250000,
                'short' => 'Thiết kế bóng rổ thập niên 80 tái sinh ngoạn mục, màu trắng xám vintage dễ phối đồ phong cách Y2K.',
                'desc' => '<p><strong>New Balance 550</strong> là biểu tượng phong cách Retro Basketball được săn lùng hàng đầu hiện nay. Form giày đầy đặn, ôm chân chắc chắn cùng phối màu trắng ngà kết hợp xám nhạt vô cùng tinh tế.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['38', '39', '40', '41', '42', '43'],
                'is_featured' => true,
            ],
            [
                'cat' => 'Giày Chạy Bộ (Running)',
                'name' => 'Nike Pegasus 40 React X Foam',
                'sku' => 'NK-PEG40-012',
                'origin' => 'Nike - Chính Hãng',
                'material' => 'Vải lưới Engineered Mesh siêu thoáng',
                'sole_height' => 3,
                'warranty' => 'Bảo hành 12 tháng',
                'season' => 'spring',
                'price' => 3400000,
                'sale_price' => 2890000,
                'short' => 'Đôi giày chạy quốc dân bền bỉ của Nike, trang bị đệm khí Zoom Air kép ở cả mũi và gót chân.',
                'desc' => '<p><strong>Nike Pegasus 40</strong> tiếp nối 4 thập kỷ thành công của dòng Pegasus lừng danh. Đệm Nike React mang lại cảm giác chuyển động êm ái, đầm chân và bền bỉ qua hàng nghìn kilomet chạy bộ.</p>',
                'thumbnail' => 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=800&auto=format&fit=crop&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=800&auto=format&fit=crop&q=80',
                ],
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'is_featured' => false,
            ]
        ];

        $careInstructions = "1. Vệ sinh định kỳ: Dùng bàn chải mềm và bọt vệ sinh chuyên dụng để làm sạch bụi bẩn trên thân và đế giày.<br>"
                          . "2. Tránh nước trực tiếp: Không ngâm giày trong xà phòng hoặc giặt bằng máy giặt vì sẽ làm bong keo và mất phom dáng.<br>"
                          . "3. Phơi giày đúng cách: Để giày khô tự nhiên nơi thoáng gió râm mát, tránh ánh nắng gắt trực tiếp làm ố vàng da.<br>"
                          . "4. Bảo quản phom dáng: Sử dụng cây giữ form giày (Shoe tree) hoặc vo viên giấy trắng độn vào bên trong khi không sử dụng.";

        foreach ($shoeProducts as $data) {
            $cat = $categories->get($data['cat']) ?? $categories->first();

            $product = Product::create([
                'category_id' => $cat->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'sku' => $data['sku'],
                'short_description' => $data['short'],
                'description' => $data['desc'],
                'care_instructions' => $careInstructions,
                'planting_season' => $data['season'], // ánh xạ phong cách / mùa
                'fruit_harvest_time' => $data['warranty'], // ánh xạ bảo hành
                'tree_age' => $data['material'], // ánh xạ chất liệu
                'tree_height_cm' => $data['sole_height'], // ánh xạ chiều cao đế cm
                'origin' => $data['origin'],
                'price' => $data['price'],
                'sale_price' => $data['sale_price'],
                'stock_quantity' => 150,
                'sold_count' => rand(15, 120),
                'views_count' => rand(100, 850),
                'thumbnail' => $data['thumbnail'],
                'weight_gram' => rand(350, 750),
                'status' => 'published',
                'is_featured' => $data['is_featured'],
                'has_variations' => true,
            ]);

            // Thư viện ảnh phụ
            if (!empty($data['images'])) {
                foreach ($data['images'] as $imgUrl) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imgUrl,
                    ]);
                }
            }

            // Gắn nhóm thuộc tính (Size)
            if ($sizeAttr) {
                $product->attributes()->syncWithoutDetaching([$sizeAttr->id]);
            }

            // Tạo các biến thể Size cho giày
            foreach ($data['sizes'] as $sizeVal) {
                $matchingVal = $sizeValues->firstWhere('value', $sizeVal);
                $varSku = $product->sku . '-SZ' . $sizeVal;

                $variation = $product->variations()->create([
                    'sku' => $varSku,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'stock_quantity' => rand(10, 40),
                    'is_default' => ($sizeVal == '40' || $sizeVal == $data['sizes'][0]),
                ]);

                if ($matchingVal) {
                    $variation->attributeValues()->sync([$matchingVal->id]);
                }
            }
        }
    }
}