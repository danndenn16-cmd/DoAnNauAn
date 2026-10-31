@extends('layouts.site')
@section('title', 'Đăng công thức')

@section('content')
<div class="mt-4 mb-4">
    <span class="label-mono is-orange">Chia sẻ</span>
    <h1 class="fw-bold mb-1" style="font-size:clamp(2rem,5vw,3rem)">Đăng công thức mới</h1>
    <p class="text-muted mb-0">
        {{ auth()->user()->isAdmin() ? 'Công thức của quản trị viên được hiển thị ngay.' : 'Công thức sẽ hiển thị công khai sau khi quản trị viên duyệt.' }}
    </p>
</div>

<form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" data-loading>
    @csrf
    @include('recipes._form', ['submitLabel' => 'Gửi công thức', 'cancelUrl' => route('home')])
</form>
@endsection