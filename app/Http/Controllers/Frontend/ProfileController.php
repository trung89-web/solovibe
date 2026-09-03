<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'addresses' => function ($q) {
                $q->orderBy('is_default', 'desc')->latest();
            },
            'orders' => function ($q) {
                $q->with(['items.product', 'items.variation.attributeValues'])->latest();
            }
        ]);
        return view('frontend.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->update($request->only('name', 'phone'));
        return redirect()->back()->with('success', 'Cập nhật thông tin thành công');
    }

    public function storeAddress(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        
        if (Auth::user()->addresses()->count() == 0) {
            $data['is_default'] = 1;
        } else {
            $data['is_default'] = 0;
        }

        UserAddress::create($data);
        return redirect()->back()->with('success', 'Thêm địa chỉ thành công');
    }

    public function updateAddress(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $address->update($request->all());
        return redirect()->back()->with('success', 'Cập nhật địa chỉ thành công');
    }

    public function destroyAddress($id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $address->delete();
        return redirect()->back()->with('success', 'Xóa địa chỉ thành công');
    }

    public function setDefaultAddress($id)
    {
        $userId = Auth::id();
        UserAddress::where('user_id', $userId)->update(['is_default' => 0]);
        UserAddress::where('id', $id)->where('user_id', $userId)->update(['is_default' => 1]);
        
        return redirect()->back()->with('success', 'Đã đặt làm địa chỉ mặc định');
    }
}
