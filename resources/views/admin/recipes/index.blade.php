@extends('layouts.site')
@section('title', 'Quản lý công thức')

@section('content')
<div class="mt-4">
    @include('admin._menu')
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm tên món...">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" @selected(request('status')==='pending' )>Chờ duyệt</option>
            <option value="approved" @selected(request('status')==='approved' )>Đã duyệt</option>
            <option value="rejected" @selected(request('status')==='rejected' )>Bị từ chối</option>
        </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-success">Lọc</button></div>
</form>

<div class="table-responsive">
    <table class="table table-bordered bg-white align-middle">
        <thead class="table-light">
            <tr>
                <th>Tên món</th>
                <th>Danh mục</th>
                <th>Người đăng</th>
                <th>Trạng thái</th>
                <th class="text-center">Nổi bật</th>
                <th style="min-width:230px">Thao tác</th>
            </tr>
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
                    @elseif($r->status === 'rejected')
                    <span class="badge bg-danger">Bị từ chối</span>
                    <div class="small text-muted mt-1">{{ $r->rejection_reason }}</div>
                    @else
                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @endif
                </td>
                <td class="text-center">
                    <form action="{{ route('admin.recipes.feature', $r) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm {{ $r->is_featured ? 'btn-orange' : 'btn-ink-outline' }}"
                            title="Chọn/bỏ nổi bật" @disabled($r->status !== 'approved')>
                            {{ $r->is_featured ? '★' : '☆' }}
                        </button>
                    </form>
                </td>
                <td>
                    @if($r->status !== 'approved')
                    <form action="{{ route('admin.recipes.approve', $r) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-success">{{ $r->status === 'rejected' ? 'Duyệt lại' : 'Duyệt' }}</button>
                    </form>
                    @endif

                    @if($r->status !== 'rejected')
                    <button type="button" class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal" data-bs-target="#rejectModal"
                        data-action="{{ route('admin.recipes.reject', $r) }}"
                        data-title="{{ $r->title }}">
                        {{ $r->status === 'approved' ? 'Gỡ bài' : 'Từ chối' }}
                    </button>
                    @endif

                    <form action="{{ route('admin.recipes.destroy', $r) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Chuyển công thức này vào thùng rác?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có công thức.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $recipes->links('pagination::bootstrap-5') }}

{{-- Hộp thoại nhập lý do từ chối --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="rejectForm" class="modal-content">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title">Từ chối công thức</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Công thức: <strong id="rejectTitle"></strong></p>
                <label class="form-label">Lý do (người đăng sẽ nhìn thấy)</label>
                <textarea name="reason" rows="3" class="form-control" maxlength="500" required
                    placeholder="Ví dụ: Nội dung không phù hợp, thiếu nguyên liệu..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ink-outline btn-sm" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-danger btn-sm">Xác nhận từ chối</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gán đường dẫn và tên món cho hộp thoại khi mở
    document.getElementById('rejectModal').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('rejectForm').action = btn.dataset.action;
        document.getElementById('rejectTitle').textContent = btn.dataset.title;
    });
</script>
@endpush