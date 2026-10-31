<x-guest-layout>
    <h1 class="auth-title">Chào mừng trở lại</h1>
    <p class="text-muted mb-4">Đăng nhập để đăng công thức, lưu món yêu thích và bình luận.</p>

    @if(session('error'))
        <div class="flash flash--danger mt-0 mb-3">{{ session('error') }}</div>
    @endif

    @if(session('status'))
        <div class="flash mt-0 mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label label-mono">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label label-mono">Mật khẩu</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label small" for="remember">Ghi nhớ đăng nhập</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small" href="{{ route('password.request') }}">Quên mật khẩu?</a>
            @endif
        </div>

        <button class="btn btn-orange w-100 py-2">Đăng nhập</button>
    </form>

    @if(config('services.google.client_id'))
        <div class="auth-divider"><span>hoặc</span></div>
        <a href="{{ route('auth.google') }}" class="btn btn-google w-100 py-2">
            <span class="g-mark">G</span>Tiếp tục với Google
        </a>
    @endif

    <p class="text-center small mt-4 mb-0">
        Chưa có tài khoản? <a href="{{ route('register') }}" class="fw-semibold">Đăng ký ngay</a>
    </p>
</x-guest-layout>