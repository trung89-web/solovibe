@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
    <div>
        <h2 class="fw-bold text-dark mb-1">💬 Lịch sử chat với {{ $user->name }}</h2>
        <p class="text-muted mb-0">Xem lại toàn bộ cuộc trò chuyện và phản hồi khách hàng</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="chat-history-box" style="max-height: 520px; overflow-y: auto; background: #f8f9fa; border-radius: 12px; padding: 20px;">
            @forelse($messages as $message)
                @php
                    $isAdmin = $message->sender_id == auth()->id();
                @endphp
                <div class="mb-3 {{ $isAdmin ? 'text-end' : 'text-start' }}">
                    <div class="d-inline-block px-3 py-2 rounded-3 {{ $isAdmin ? 'bg-dark text-white' : 'bg-white border' }}" style="max-width: 75%;">
                        <div class="small text-uppercase fw-semibold mb-1 {{ $isAdmin ? 'text-white-50' : 'text-muted' }}">
                            {{ $isAdmin ? 'Admin' : $user->name }}
                        </div>
                        <div>{{ $message->content }}</div>
                        <div class="small mt-1 {{ $isAdmin ? 'text-white-50' : 'text-muted' }}">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                    Chưa có tin nhắn nào giữa admin và khách hàng này.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
