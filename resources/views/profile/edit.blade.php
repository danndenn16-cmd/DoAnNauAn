@extends('layouts.site')
@section('title', 'Hồ sơ cá nhân')

@section('content')
@php $user = auth()->user(); @endphp

<div class="row justify-content-center mt-4">
    <div class="col-lg-8">

        {{-- Đầu trang --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="avatar avatar--lg">{{ mb_substr($user->name, 0, 1) }}</span>
            <div>
                <h1 class="mb-0 fw-bold" style="font-size:2rem">{{ $user->name }}</h1>
                <div class="label-mono">
                    {{ $user->isAdmin() ? 'Quản trị viên' : 'Thành viên' }}
                    · Tham gia {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>

        {{-- 1. Thông tin cá nhân --}}
        <section class="profile-card">
            <h2>Thông tin cá nhân</h2>
            <p class="text-muted small mb-3">Cập nhật tên hiển thị và địa chỉ email của bạn.</p>

            @if(session('status') === 'profile-updated')
            <div class="flash mt-0 mb-3">Đã lưu thông tin.</div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label label-mono">Họ và tên</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="form-control @error('name') is-invalid @enderror" required autocomplete="name">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label label-mono">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-orange">Lưu thay đổi</button>
            </form>
        </section>

        {{-- 2. Đổi mật khẩu --}}
        <section class="profile-card">
            <h2>Đổi mật khẩu</h2>
            <p class="text-muted small mb-3">Nên dùng mật khẩu dài và khó đoán để bảo vệ tài khoản.</p>

            @if(session('status') === 'password-updated')
            <div class="flash mt-0 mb-3">Đã đổi mật khẩu.</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label label-mono">Mật khẩu hiện tại</label>
                    <input id="current_password" type="password" name="current_password"
                        class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                        autocomplete="current-password">
                    @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label label-mono">Mật khẩu mới</label>
                    <input id="new_password" type="password" name="password"
                        class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label label-mono">Nhập lại mật khẩu mới</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-orange">Đổi mật khẩu</button>
            </form>
        </section>

        {{-- 3. Xóa tài khoản (ẩn với admin để không mất tài khoản quản trị) --}}
        @unless($user->isAdmin())
        <section class="profile-card profile-card--danger">
            <h2>Xóa tài khoản</h2>
            <p class="text-muted small mb-3">
                Khi xóa tài khoản, toàn bộ công thức, bình luận và mục yêu thích của bạn sẽ bị xóa vĩnh viễn.
            </p>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                Xóa tài khoản
            </button>
        </section>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('profile.destroy') }}" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Bạn chắc chắn muốn xóa tài khoản?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">Hành động này không thể hoàn tác. Nhập mật khẩu để xác nhận.</p>
                        <input type="password" name="password" placeholder="Mật khẩu"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror">
                        @error('password', 'userDeletion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ink-outline btn-sm" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Nếu nhập sai mật khẩu thì mở lại hộp thoại để người dùng thấy lỗi --}}
        @if($errors->userDeletion->isNotEmpty())
        @push('scripts')
        <script>
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        </script>
        @endpush
        @endif
        @endunless

    </div>
</div>
@endsection