<x-guest-layout>
    <h1 class="auth-title">Quên mật khẩu?</h1>
    <p class="text-muted mb-4">Nhập email, chúng tôi sẽ gửi liên kết để bạn đặt lại mật khẩu.</p>

    @if(session('status'))
    <div class="flash mt-0 mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label label-mono">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-orange w-100 py-2">Gửi liên kết đặt lại</button>
    </form>

    <p class="text-center small mt-4 mb-0"><a href="{{ route('login') }}">← Quay lại đăng nhập</a></p>
</x-guest-layout>