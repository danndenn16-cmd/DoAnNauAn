@extends('layouts.site')
@section('title', 'Sửa công thức')

@section('content')
<div class="mt-4 mb-4">
    <span class="label-mono is-orange">Chỉnh sửa</span>
    <h1 class="fw-bold mb-1" style="font-size:clamp(2rem,5vw,3rem)">{{ $recipe->title }}</h1>
    @unless(auth()->user()->isAdmin())
    <p class="text-muted mb-0">Sau khi lưu, công thức sẽ được gửi duyệt lại.</p>
    @endunless
</div>

<form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data" data-loading>
    @csrf
    @method('PUT')
    @include('recipes._form', ['submitLabel' => 'Lưu thay đổi', 'cancelUrl' => route('recipes.mine')])
</form>
@endsection