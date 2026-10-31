<x-guest-layout>
    <h1 class="auth-title">Tạo tài khoản</h1>
    <p class="text-muted mb-4">Tham gia cộng đồng và chia sẻ công thức của riêng bạn.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label label-mono">Họ và tên</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                class="form-control @error('name') is-invalid @enderror"
                required autofocus autocomplete="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label label-mono">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                required autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label label-mono">Mật khẩu</label>
            <input id="password" type="password" name="password"
                class="form-control @error('password') is-invalid @enderror"
                required autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label label-mono">Nhập lại mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                required autocomplete="new-password">
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-orange w-100 py-2">Đăng ký</button>
    </form>
@if(config('services.google.client_id'))
        <div class="auth-divider"><span>hoặc</span></div>
        <a href="{{ route('auth.google') }}" class="btn btn-google w-100 py-2">
            <span class="g-mark">G</span>Tiếp tục với Google
        </a>
    @endif
    <p class="text-center small mt-4 mb-0">
        Đã có tài khoản? <a href="{{ route('login') }}" class="fw-semibold">Đăng nhập</a>
    </p>
</x-guest-layout>