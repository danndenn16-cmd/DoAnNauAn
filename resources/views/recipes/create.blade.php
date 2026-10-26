@extends('layouts.site')
@section('title', 'Đăng công thức')

@section('content')
<h2 class="mb-3">Đăng công thức mới</h2>

<form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" class="card card-body shadow-sm">
    @csrf
    @include('recipes._form')
    <div>
        <button class="btn btn-success">Lưu công thức</button>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Hủy</a>
    </div>
</form>
@endsection