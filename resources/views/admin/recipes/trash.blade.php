@extends('layouts.admin')
@section('title', 'Thùng rác')

@section('content')
<div class="mt-4">
    @include('admin._menu')
</div>

<div class="table-responsive">
    <table class="table table-bordered bg-white align-middle">
        <thead class="table-light">
            <tr>
                <th>Tên món</th>
                <th>Người đăng</th>
                <th>Ngày xóa</th>
                <th style="min-width:230px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recipes as $r)
            <tr>
                <td>{{ $r->title }}</td>
                <td>{{ $r->user->name }}</td>
                <td>{{ $r->deleted_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form action="{{ route('admin.recipes.restore', $r->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-success">Khôi phục</button>
                    </form>
                    <form action="{{ route('admin.recipes.force', $r->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Xóa vĩnh viễn? Không thể hoàn tác!')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Thùng rác trống.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $recipes->links('pagination::bootstrap-5') }}
@endsection