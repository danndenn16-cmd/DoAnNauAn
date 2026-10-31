@extends('layouts.site')
@section('title', 'Quản lý danh mục')

@section('content')
<div class="mt-4">
    @include('admin._menu')
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm danh mục...">
        <button class="btn btn-success">Tìm</button>
    </form>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-orange">+ Thêm danh mục</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered bg-white align-middle">
        <thead class="table-light">
            <tr>
                <th style="width:60px">#</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th class="text-center">Số công thức</th>
                <th style="min-width:150px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $c)
            <tr>
                <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                <td>
                    <strong>{{ $c->name }}</strong>
                    <div class="label-mono">{{ $c->slug }}</div>
                </td>
                <td class="text-muted">{{ \Illuminate\Support\Str::limit($c->description, 70) }}</td>
                <td class="text-center">{{ $c->recipes_count }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Xóa danh mục này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Chưa có danh mục nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $categories->links('pagination::bootstrap-5') }}
@endsection