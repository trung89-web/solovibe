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
        // Chỉ lấy các danh mục đang hiển thị
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction(); // Bắt đầu transaction

            $data = $request->validated();
            
            // Xử lý tự động tạo slug nếu để trống
            $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
            
            // Xử lý logic check trùng slug (nối hậu tố nếu trùng)
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Product::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }

            // Mặc định các thông số
            $data['is_featured'] = $request->has('is_featured');
            $data['sold_count'] = 0;
            $data['views_count'] = 0;

            // Xử lý upload Thumbnail (Ảnh đại diện)
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
            }

            // Lưu dữ liệu vào bảng products
            $product = Product::create($data);

            // Xử lý upload Gallery (Nhiều ảnh phụ)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => $index,
                        'is_primary' => $index === 0, // Ảnh đầu tiên làm ảnh chính của gallery
                    ]);
                }
            }

            DB::commit(); // Xác nhận lưu thành công
            return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load('images'); // Eager load thư viện ảnh
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
            
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }

            $data['is_featured'] = $request->has('is_featured');

            // Xử lý upload Thumbnail (xoá ảnh cũ nếu có ảnh mới)
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
            }

            $product->update($data);

            // Xử lý thêm ảnh vào Gallery (không xoá ảnh cũ, chỉ append thêm)
            if ($request->hasFile('images')) {
                $maxSort = $product->images()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => $maxSort + 1 + $index,
                        'is_primary' => false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
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