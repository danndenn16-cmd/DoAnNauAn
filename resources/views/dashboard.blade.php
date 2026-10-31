@extends('layouts.site')
@section('title', 'Quản lý công thức')

@section('content')
<ul class="nav nav-pills mb-4">
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Tổng quan</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.recipes.index') }}">Công thức</a></li>
</ul>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm tên món...">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" @selected(request('status') === 'pending')>Chờ duyệt</option>
            <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
        </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-success">Lọc</button></div>
</form>

<table class="table table-bordered bg-white align-middle">
    <thead class="table-light">
        <tr><th>Tên món</th><th>Danh mục</th><th>Người đăng</th><th>Trạng thái</th><th style="width:200px">Thao tác</th></tr>
    </thead>
    <tbody>
        @forelse($recipes as $r)
            <tr>
                <td><a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a></td>
                <td>{{ $r->category->name }}</td>
                <td>{{ $r->user->name }}</td>
                <td>
                    @if($r->status === 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @else
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @endif
                </td>
                <td>
                    @if($r->status === 'pending')
                        <form action="{{ route('admin.recipes.approve', $r) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-success">Duyệt</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.recipes.destroy', $r) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Chuyển vào thùng rác?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">Không có công thức.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $recipes->links('pagination::bootstrap-5') }}
@endsection