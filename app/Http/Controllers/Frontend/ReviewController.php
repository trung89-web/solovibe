<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_item_id' => ['required', 'exists:order_items,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'order_item_id.exists' => 'Sản phẩm này không tồn tại trong đơn hàng của bạn.',
            'rating.between' => 'Đánh giá phải từ 1 đến 5 sao.',
        ]);

        $orderItem = OrderItem::with('order')->findOrFail($validated['order_item_id']);

        abort_if($orderItem->order->user_id !== Auth::id(), 403, 'Bạn không có quyền đánh giá sản phẩm này.');

        if ($orderItem->order->order_status !== 'completed') {
            return back()->withErrors(['order_item_id' => 'Chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã hoàn thành.']);
        }

        $alreadyReviewed = ProductReview::where('order_item_id', $orderItem->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyReviewed) {
            return back()->withErrors(['order_item_id' => 'Bạn đã đánh giá sản phẩm này rồi.']);
        }

        $review = ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $orderItem->product_id,
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm.');
    }

    public function destroy(ProductReview $review)
    {
        abort_if($review->user_id !== Auth::id(), 403, 'Bạn không có quyền xóa đánh giá này.');

        $review->delete();

        return back()->with('success', 'Đánh giá đã được xóa thành công.');
    }
}
