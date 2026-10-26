@extends('layouts.site')
@section('title', 'Sửa công thức')

@section('content')
<h2 class="mb-3">Sửa công thức</h2>

<form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data" class="card card-body shadow-sm">
    @csrf
    @method('PUT')
    @include('recipes._form')
    <div>
        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('recipes.mine') }}" class="btn btn-outline-secondary">Hủy</a>
    </div>
</form>
@endsection