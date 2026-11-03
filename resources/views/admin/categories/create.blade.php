@extends('layouts.admin')
@section('title', 'Thêm danh mục')

@section('content')
<div class="mt-4">
    @include('admin._menu')
</div>

<h2 class="mb-3">Thêm danh mục</h2>

<form action="{{ route('admin.categories.store') }}" method="POST" class="card card-body shadow-sm">
    @csrf
    @include('admin.categories._form')
    <div>
        <button class="btn btn-orange">Lưu</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-ink-outline">Hủy</a>
    </div>
</form>
@endsection