<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        // Lấy danh sách nhóm thuộc tính kèm theo các giá trị con của nó
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            // values là một chuỗi cách nhau bằng dấu phẩy (VD: "Màu đỏ, Màu xanh")
            'values' => 'required|string' 
        ]);

        $attribute = Attribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => Attribute::max('sort_order') + 1,
        ]);

        // Tách chuỗi values thành mảng và lưu vào bảng attribute_values
        $valuesArray = array_map('trim', explode(',', $request->values));
        foreach ($valuesArray as $index => $val) {
            if (!empty($val)) {
                $attribute->values()->create([
                    'value' => $val,
                    'sort_order' => $index
                ]);
            }
        }

        return back()->with('success', 'Đã thêm nhóm thuộc tính thành công!');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete(); // Sẽ tự động xóa các values con nhờ cascadeOnDelete trong DB
        return back()->with('success', 'Đã xóa nhóm thuộc tính!');
    }
}