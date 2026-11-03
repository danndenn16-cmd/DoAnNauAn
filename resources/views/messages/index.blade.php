@extends('layouts.site')
@section('title', 'Tin nhắn')

@section('content')
<div class="mt-4 mb-3">
    <span class="label-mono is-orange">Hỗ trợ</span>
    <h1 class="fw-bold mb-1" style="font-size:clamp(2rem,5vw,3rem)">Trao đổi với quản trị viên</h1>
    <p class="text-muted mb-0">Gửi câu hỏi hoặc phản hồi, quản trị viên sẽ trả lời tại đây. Tin mới tự hiện, không cần tải lại trang.</p>
</div>

<div class="dm-box">
    <div class="dm-head">
        <span class="avatar">Q</span>
        <div>
            <strong>Quản trị viên</strong>
            <div class="label-mono">Cook cùng 1045</div>
        </div>
    </div>

    @include('messages._thread', [
        'viewer'    => auth()->user(),
        'pollUrl'   => route('messages.poll'),
        'storeUrl'  => route('messages.store'),
        'otherLabel' => 'Quản trị viên',
        'emptyText' => 'Chưa có tin nhắn nào. Hãy gửi câu hỏi đầu tiên cho quản trị viên.',
    ])
</div>
@endsection