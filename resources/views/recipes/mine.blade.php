@extends('layouts.site')
@section('title', 'Công thức của tôi')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mt-4 mb-3">
    <div>
        <span class="label-mono is-orange">Tài khoản</span>
        <h1 class="fw-bold mb-0" style="font-size:clamp(2rem,5vw,3rem)">Công thức của tôi</h1>
    </div>
    <a href="{{ route('recipes.create') }}" class="btn btn-orange">+ Đăng công thức</a>
</div>

<div class="chips mb-4">
    @foreach([
    '' => ['Tất cả', 'all'],
    'pending' => ['Chờ duyệt', 'pending'],
    'approved' => ['Đã duyệt', 'approved'],
    'rejected' => ['Bị từ chối', 'rejected'],
    ] as $key => [$label, $countKey])
    <a class="chip {{ request('status', '') === (string) $key ? 'is-active' : '' }}"
        href="{{ route('recipes.mine', $key ? ['status' => $key] : []) }}">
        {{ $label }} <small>{{ $counts[$countKey] }}</small>
    </a>
    @endforeach
</div>

@forelse($recipes as $r)
<div class="my-recipe">
    <a href="{{ route('recipes.show', $r) }}" class="my-recipe__img">
        <img src="{{ $r->image_url }}" alt="{{ $r->title }}" loading="lazy">
    </a>

    <div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($r->status === 'approved')
            <span class="badge bg-success">Đã duyệt</span>
            @elseif($r->status === 'rejected')
            <span class="badge bg-danger">Bị từ chối</span>
            @else
            <span class="badge bg-warning">Chờ duyệt</span>
            @endif
            <span class="label-mono">{{ $r->category->name }}</span>
        </div>
        <h3><a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a></h3>
        <div class="small text-muted">⏱ {{ $r->cook_time }} phút · Đăng ngày {{ $r->created_at->format('d/m/Y') }}</div>

        @if($r->status === 'rejected' && $r->rejection_reason)
        <div class="reject-note">Lý do: {{ $r->rejection_reason }}</div>
        @endif
    </div>

    <div class="my-recipe__actions">
        <a href="{{ route('recipes.edit', $r) }}" class="btn btn-sm btn-ink-outline">Sửa</a>
        <form action="{{ route('recipes.destroy', $r) }}" method="POST"
            onsubmit="return confirm('Xóa công thức này?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Xóa</button>
        </form>
    </div>
</div>
@empty
<div class="empty">
    Chưa có công thức nào trong mục này.
    <div><a href="{{ route('recipes.create') }}" class="btn btn-orange mt-3">Đăng công thức đầu tiên</a></div>
</div>
@endforelse

<div class="mt-4 d-flex justify-content-center">
    {{ $recipes->links('pagination::bootstrap-5') }}
</div>
@endsection