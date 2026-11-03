@extends('layouts.admin')
@section('title', 'Thành viên')

@section('content')
@include('admin._menu', ['title' => 'Thành viên và phân quyền'])

{{-- Bảng quyền hạn của từng vai trò --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="role-card">
            <h3>Thành viên</h3>
            <ul>
                <li>Đăng, sửa, xóa công thức của mình</li>
                <li>Yêu thích, bình luận</li>
                <li>Nhắn tin với quản trị viên</li>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="role-card">
            <h3>Biên tập viên</h3>
            <ul>
                <li>Mọi quyền của thành viên</li>
                <li>Duyệt, từ chối, xóa công thức của người khác</li>
                <li>Xóa bình luận, dùng thùng rác</li>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="role-card">
            <h3>Quản trị viên</h3>
            <ul>
                <li>Mọi quyền của biên tập viên</li>
                <li>Chọn món nổi bật, quản lý danh mục</li>
                <li>Cấp quyền thành viên, nhận tin nhắn</li>
            </ul>
        </div>
    </div>
</div>

<div class="chips mb-3">
    <a class="chip {{ request('role') ? '' : 'is-active' }}"
       href="{{ route('admin.users.index', array_filter(['q' => request('q')])) }}">Tất cả <small>{{ $counts->sum() }}</small></a>
    @foreach(\App\Models\User::ROLES as $key => $label)
        <a class="chip {{ request('role') === $key ? 'is-active' : '' }}"
           href="{{ route('admin.users.index', array_filter(['role' => $key, 'q' => request('q')])) }}">{{ $label }} <small>{{ $counts[$key] ?? 0 }}</small></a>
    @endforeach
</div>

<form method="GET" class="d-flex gap-2 mb-3" style="max-width:480px">
    @if(request('role'))
        <input type="hidden" name="role" value="{{ request('role') }}">
    @endif
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên hoặc email...">
    <button class="btn btn-success">Tìm</button>
</form>

<div class="table-responsive">
<table class="table bg-white align-middle">
    <thead class="table-light">
        <tr>
            <th>Thành viên</th>
            <th>Vai trò hiện tại</th>
            <th class="text-center">Công thức</th>
            <th>Tham gia</th>
            <th style="min-width:270px">Cấp quyền</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $u)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="avatar">{{ mb_substr($u->name, 0, 1) }}</span>
                        <span>
                            {{ $u->name }}
                            @if($u->id === auth()->id()) <span class="label-mono">(bạn)</span> @endif
                            <div class="small text-muted">{{ $u->email }}</div>
                        </span>
                    </div>
                </td>
                <td>
                    @if($u->role === 'admin')
                        <span class="badge bg-danger">{{ $u->roleLabel() }}</span>
                    @elseif($u->role === 'moderator')
                        <span class="badge bg-warning">{{ $u->roleLabel() }}</span>
                    @else
                        <span class="badge bg-secondary">{{ $u->roleLabel() }}</span>
                    @endif
                </td>
                <td class="text-center">{{ $u->recipes_count }}</td>
                <td>{{ $u->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($u->id === auth()->id())
                        <span class="text-muted small">Không thể đổi quyền của chính mình</span>
                    @else
                        <form action="{{ route('admin.users.role', $u) }}" method="POST" class="d-flex gap-2"
                              data-name="{{ $u->name }}" onsubmit="return confirmRole(this)">
                            @csrf
                            @method('PATCH')
                            <select name="role" class="form-select form-select-sm">
                                @foreach(\App\Models\User::ROLES as $key => $label)
                                    <option value="{{ $key }}" @selected($u->role === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-orange">Lưu</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">Không tìm thấy thành viên nào.</td></tr>
        @endforelse
    </tbody>
</table>
</div>

{{ $users->links('pagination::bootstrap-5') }}
@endsection

@push('scripts')
<script>
    // Hỏi xác nhận trước khi đổi vai trò
    function confirmRole(form) {
        const sel = form.elements.role;
        return confirm('Đổi vai trò của ' + form.dataset.name + ' thành "' + sel.options[sel.selectedIndex].text + '"?');
    }
</script>
@endpush