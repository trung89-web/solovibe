@extends('frontend.layouts.app')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        @include('components.filter-sidebar')
    </div>

    <!-- Product Grid -->
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Danh sách sản phẩm ({{ $products->total() }})</h4>
        </div>

        @if($products->isEmpty())
            <div class="alert alert-warning text-center">
                Không tìm thấy sản phẩm nào phù hợp với điều kiện lọc.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
                @foreach($products as $product)
                    <div class="col">
                        @include('components.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection