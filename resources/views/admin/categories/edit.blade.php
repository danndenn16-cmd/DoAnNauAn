@extends('layouts.site')
@section('title', 'Sửa danh mục')

@section('content')
<div class="mt-4">
    @include('admin._menu')
</div>

<h2 class="mb-3">Sửa danh mục</h2>

<form action="{{ route('admin.categories.update', $category) }}" method="POST" class="card card-body shadow-sm">
    @csrf
    @method('PUT')
    @include('admin.categories._form')
    <div>
        <button class="btn btn-orange">Cập nhật</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-ink-outline">Hủy</a>
    </div>
</form>
@endsection