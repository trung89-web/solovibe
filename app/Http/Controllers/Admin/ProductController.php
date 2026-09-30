<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Attribute;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function index()
    {
        // Lấy danh sách sản phẩm kèm theo danh mục, sắp xếp mới nhất
        $products = Product::with('category')->orderBy('id', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        
        // LẤY THÊM DỮ LIỆU THUỘC TÍNH
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu cơ bản (Bạn có thể bổ sung thêm các rule của bạn)
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku',
        ]);

        try {
            // Bật Transaction: Nếu 1 bảng lưu lỗi, toàn bộ sẽ Rollback, không tạo ra dữ liệu rác
            DB::beginTransaction();

            $hasVariations = $request->has('has_variations');

            // 2. Tính toán Giá và Tồn kho cho Sản phẩm cha (Lấy giá Min và tổng tồn kho để dễ hiển thị)
            $basePrice = $request->price ?? 0;
            $baseSalePrice = $request->sale_price ?? null;
            $totalStock = $request->stock_quantity ?? 0;

            if ($hasVariations && $request->has('variations')) {
                $variations = collect($request->variations);
                $basePrice = $variations->min('price'); 
                $totalStock = $variations->sum('stock_quantity');
            }

            // 3. Tạo Sản phẩm cha (Bảng products)
            // (Lưu ý: Các biến như thumbnail, description bạn hãy giữ nguyên như code upload cũ của bạn nhé, 
            // ở đây tôi lược bớt để tập trung vào logic Biến thể)
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'sku' => $request->sku,
                'origin' => $request->origin,
                'tree_age' => $request->tree_age ?? $request->material,
                'tree_height_cm' => $request->tree_height_cm ?? $request->sole_height_cm,
                'fruit_harvest_time' => $request->fruit_harvest_time ?? $request->warranty,
                'planting_season' => $request->planting_season ?? 'all_year',
                'short_description' => $request->short_description,
                'description' => $request->description,
                'care_instructions' => $request->care_instructions,
                'thumbnail' => $thumbnailPath,
                'price' => $basePrice,
                'sale_price' => $baseSalePrice,
                'stock_quantity' => $totalStock,
                'has_variations' => $hasVariations,
                'status' => $request->status ?? 'published',
                'is_featured' => $request->has('is_featured'),
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }

            // 4. Xử lý lưu Phân loại hàng hóa (Variations)
            if ($hasVariations && $request->has('variations')) {
                
                // Đồng bộ bảng trung gian product_attributes (Lưu lại việc SP này dùng nhóm thuộc tính nào)
                if ($request->has('attribute_values')) {
                    $attributeIds = array_keys($request->attribute_values);
                    $product->attributes()->sync($attributeIds);
                }

                foreach ($request->variations as $index => $varData) {
                    // Tạo SKU riêng cho từng biến thể, nếu admin ko nhập thì nối thêm số (VD: SP01-1)
                    $varSku = !empty($varData['sku']) ? $varData['sku'] : $product->sku . '-' . ($index + 1);

                    // Tạo Biến thể (Bảng product_variations)
                    $variation = $product->variations()->create([
                        'sku' => $varSku,
                        'price' => $varData['price'],
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock_quantity' => $varData['stock_quantity'],
                        'is_default' => ($index == 0), // Lấy tổ hợp đầu tiên làm mặc định hiển thị
                    ]);

                    // Liên kết tổ hợp Giá trị (VD: Gắn 'Cây giống', 'Ghép mắt' vào biến thể này)
                    if (!empty($varData['attribute_value_ids'])) {
                        // JS đã gửi về chuỗi "1,4" -> Tách ra thành mảng [1, 4]
                        $valIds = explode(',', $varData['attribute_value_ids']);
                        $variation->attributeValues()->sync($valIds);
                    }
                }
            } else {
                // NƯỚC ĐI CHIẾN THUẬT:
                // Nếu sản phẩm KHÔNG CÓ phân loại, ta tự động sinh 1 "Biến thể ngầm" (Default Variation).
                // Nhờ việc này, Giỏ hàng về sau CHỈ CẦN làm việc với bảng `product_variations` mà không lo bị gãy logic.
                $product->variations()->create([
                    'sku' => $product->sku . '-DEFAULT',
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'stock_quantity' => $product->stock_quantity,
                    'is_default' => true,
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Đã thêm sản phẩm kèm phân loại thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Product $product)
    {
        // 1. Tự động load thêm các mối quan hệ (Variations và Attributes)
        $product->load(['variations.attributeValues', 'attributes']);
        
        $categories = Category::all();
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();

        // 2. Lấy danh sách ID của các thuộc tính đang được tick (để check form)
        $selectedAttributeIds = $product->attributes->pluck('id')->toArray();

        // 3. Đóng gói danh sách Biến thể cũ thành mảng để truyền xuống JavaScript
        $existingVariations = $product->variations->map(function ($var) {
            return [
                'sku' => $var->sku,
                'price' => $var->price,
                'sale_price' => $var->sale_price,
                'stock_quantity' => $var->stock_quantity,
                // Lấy mảng ID các giá trị (VD: [1, 4]) và sắp xếp tăng dần để làm Key
                'attribute_value_ids' => $var->attributeValues->pluck('id')->sort()->values()->toArray(),
            ];
        });

        return view('admin.products.edit', compact('product', 'categories', 'attributes', 'selectedAttributeIds', 'existingVariations'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,' . $product->id, // Bỏ qua trùng lặp với chính nó
        ]);

        try {
            DB::beginTransaction();

            $hasVariations = $request->has('has_variations');
            $basePrice = $request->price ?? 0;
            $baseSalePrice = $request->sale_price ?? null;
            $totalStock = $request->stock_quantity ?? 0;

            if ($hasVariations && $request->has('variations')) {
                $variations = collect($request->variations);
                $basePrice = $variations->min('price'); 
                $totalStock = $variations->sum('stock_quantity');
            }

            // 1. Cập nhật thông tin cơ bản
            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'sku' => $request->sku,
                'origin' => $request->origin,
                'tree_age' => $request->tree_age,
                'tree_height_cm' => $request->tree_height_cm,
                'fruit_harvest_time' => $request->fruit_harvest_time,
                'planting_season' => $request->planting_season,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'care_instructions' => $request->care_instructions,
                'price' => $basePrice,
                'sale_price' => $baseSalePrice,
                'stock_quantity' => $totalStock,
                'has_variations' => $hasVariations,
                'status' => $request->status,
                'is_featured' => $request->has('is_featured'),
            ]);

            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $product->update([
                    'thumbnail' => $request->file('thumbnail')->store('products', 'public'),
                ]);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }

            // 2. Cập nhật Biến thể
            if ($hasVariations && $request->has('variations')) {
                // Đồng bộ bảng nhóm thuộc tính
                if ($request->has('attribute_values')) {
                    $product->attributes()->sync(array_keys($request->attribute_values));
                }

                // XÓA CŨ - THÊM MỚI
                $product->variations()->forceDelete();

                foreach ($request->variations as $index => $varData) {
                    $varSku = !empty($varData['sku']) ? $varData['sku'] : $product->sku . '-' . ($index + 1);

                    $variation = $product->variations()->create([
                        'sku' => $varSku,
                        'price' => $varData['price'],
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock_quantity' => $varData['stock_quantity'],
                        'is_default' => ($index == 0),
                    ]);

                    if (!empty($varData['attribute_value_ids'])) {
                        $valIds = explode(',', $varData['attribute_value_ids']);
                        $variation->attributeValues()->sync($valIds);
                    }
                }
            } else {
                // Nếu tắt biến thể đi, gỡ bỏ thuộc tính và tạo lại biến thể DEFAULT
                $product->attributes()->detach();
                $product->variations()->forceDelete();
                
                $product->variations()->create([
                    'sku' => $product->sku . '-DEFAULT',
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'stock_quantity' => $product->stock_quantity,
                    'is_default' => true,
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $product)
    {
        // Sử dụng SoftDelete (đã cấu hình trong Model Product)
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    // Route phụ trợ để xoá từng ảnh trong Gallery khi đang ở màn hình Edit
    public function destroyImage(ProductImage $productImage)
    {
        // Xoá file vật lý
        if (Storage::disk('public')->exists($productImage->image_path)) {
            Storage::disk('public')->delete($productImage->image_path);
        }
        
        // Xoá bản ghi trong DB
        $productImage->delete();
        
        return back()->with('success', 'Đã xoá ảnh khỏi thư viện!');
    }
}