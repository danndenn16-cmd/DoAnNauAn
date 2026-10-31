@extends('layouts.site')
@section('title', 'Không tìm thấy trang')

@section('content')
<div class="error-page">
    <div class="error-page__code">404</div>
    <h1 class="fw-bold">Món này chưa có trong thực đơn</h1>
    <p class="text-muted">Trang bạn tìm không tồn tại hoặc đã bị xóa.</p>
    <div class="d-flex flex-wrap justify-content-center gap-2">
        <a class="btn btn-orange" href="{{ route('home') }}">Về trang chủ</a>
        <a class="btn btn-ink-outline" href="{{ route('recipes.random') }}">🎲 Gợi ý món ngẫu nhiên</a>
    </div>
</div>
@endsection