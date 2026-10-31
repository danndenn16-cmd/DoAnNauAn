@extends('layouts.site')
@section('title', 'Không có quyền truy cập')

@section('content')
<div class="error-page">
    <div class="error-page__code">403</div>
    <h1 class="fw-bold">Khu vực dành riêng cho bếp trưởng</h1>
    <p class="text-muted">{{ $exception->getMessage() ?: 'Bạn không có quyền truy cập trang này.' }}</p>
    <a class="btn btn-orange" href="{{ route('home') }}">Về trang chủ</a>
</div>
@endsection