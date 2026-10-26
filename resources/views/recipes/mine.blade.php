@extends('layouts.site')
@section('title', 'Công thức của tôi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Công thức của tôi</h2>
    <a href="{{ route('recipes.create') }}" class="btn btn-success">+ Đăng công thức</a>
</div>

<table class="table table-bordered bg-white align-middle">
    <thead class="table-light">
        <tr>
            <th>Tên món</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th style="width:220px">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse($recipes as $r)
            <tr>
                <td><a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a></td>
                <td>{{ $r->category->name }}</td>
                <td>
                    @if($r->status === 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @else
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('recipes.edit', $r) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('recipes.destroy', $r) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Xóa công thức này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted">Bạn chưa đăng công thức nào.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $recipes->links('pagination::bootstrap-5') }}
@endsection