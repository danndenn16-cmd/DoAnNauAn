<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') · Quản trị Cook cùng 1045</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="adm-body">

@php
    $me = auth()->user();
    $adminUnread = $me->isAdmin() ? $me->unreadMessagesCount() : 0;
@endphp

<div class="adm">
    {{-- Thanh bên: cố định trên máy tính, trượt ra trên điện thoại (Bootstrap offcanvas) --}}
    <div class="adm-sidewrap">
        <aside class="adm-side offcanvas-lg offcanvas-start" tabindex="-1" id="admSide" aria-labelledby="admSideTitle">
            <div class="offcanvas-header d-lg-none">
                <span class="adm-brand" id="admSideTitle"><i></i>Cook cùng 1045</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#admSide" aria-label="Đóng"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column">
                <a class="adm-brand d-none d-lg-inline-flex" href="{{ route('admin.dashboard') }}"><i></i>Cook cùng 1045</a>

                <div class="adm-user">
                    <span class="avatar">{{ mb_substr($me->name, 0, 1) }}</span>
                    <div>
                        <strong>{{ $me->name }}</strong>
                        <small>{{ $me->roleLabel() }}</small>
                    </div>
                </div>

                <nav class="adm-nav">
                    <a class="adm-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">📊 Tổng quan</a>
                    <a class="adm-link {{ request()->routeIs('admin.recipes.index') ? 'is-active' : '' }}" href="{{ route('admin.recipes.index') }}">
                        🍲 Công thức
                        @if(($pendingCount ?? 0) > 0) <span class="count">{{ $pendingCount }}</span> @endif
                    </a>
                    <a class="adm-link {{ request()->routeIs('admin.recipes.trash') ? 'is-active' : '' }}" href="{{ route('admin.recipes.trash') }}">🗑 Thùng rác</a>

                    @if($me->isAdmin())
                        <div class="adm-section">Chỉ quản trị viên</div>
                        <a class="adm-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}" href="{{ route('admin.categories.index') }}">🗂 Danh mục</a>
                        <a class="adm-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">👥 Thành viên và phân quyền</a>
                        <a class="adm-link {{ request()->routeIs('admin.messages.*') ? 'is-active' : '' }}" href="{{ route('admin.messages.index') }}">
                            💬 Tin nhắn
                            <span class="msg-badge" data-msg-badge @if($adminUnread < 1) hidden @endif>{{ $adminUnread }}</span>
                        </a>
                    @endif
                </nav>

                <div class="mt-auto pt-4">
                    <a class="adm-link" href="{{ route('home') }}">← Về website</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="adm-link">⎋ Đăng xuất</button>
                    </form>
                </div>
            </div>
        </aside>
    </div>

    <div class="adm-main">
        <header class="adm-top">
            <button type="button" class="adm-toggle d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#admSide" aria-controls="admSide" aria-label="Mở menu quản trị">☰</button>
            <span class="label-mono">Bảng điều khiển · {{ $me->roleLabel() }}</span>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="avatar">{{ mb_substr($me->name, 0, 1) }}</span>
                <span class="d-none d-sm-inline small">{{ $me->name }}</span>
            </div>
        </header>

        <main class="adm-content">
            @yield('content')
        </main>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" id="toastStack" style="z-index:2000;margin-top:3.5rem"></div>

@php
    $toasts = [];
    if (session('success')) { $toasts[] = ['success', session('success')]; }
    if (session('error'))   { $toasts[] = ['error', session('error')]; }
    if ($errors->any())     { $toasts[] = ['error', 'Vui lòng kiểm tra lại thông tin đã nhập.']; }
@endphp

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // Toast dùng chung: showToast('nội dung', 'success' | 'error' | 'info')
    window.showToast = function (message, type) {
        type = type || 'success';
        const icons = { success: '✓', error: '⚠', info: 'ℹ' };
        const el = document.createElement('div');
        el.className = 'toast app-toast app-toast--' + type;
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        el.setAttribute('aria-atomic', 'true');
        el.innerHTML =
            '<div class="d-flex align-items-start gap-2 p-3">' +
            '<span class="app-toast__icon"></span>' +
            '<div class="toast-body p-0 flex-grow-1"></div>' +
            '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Đóng"></button>' +
            '</div>';
        el.querySelector('.app-toast__icon').textContent = icons[type] || icons.info;
        el.querySelector('.toast-body').textContent = message;
        document.getElementById('toastStack').appendChild(el);
        el.addEventListener('hidden.bs.toast', () => el.remove());
        new bootstrap.Toast(el, { delay: type === 'error' ? 7000 : 4000 }).show();
    };

    @json($toasts).forEach(function (t) { window.showToast(t[1], t[0]); });

    // Cập nhật huy hiệu tin nhắn chưa đọc trên thanh bên (khoảng 20 giây một lần)
    const badges = document.querySelectorAll('[data-msg-badge]');
    if (badges.length) {
        setInterval(function () {
            if (document.hidden) return;
            fetch('{{ route('messages.unread') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.ok ? r.json() : null)
                .then(function (d) {
                    if (!d) return;
                    badges.forEach(function (b) { b.textContent = d.count; b.hidden = d.count < 1; });
                })
                .catch(function () {});
        }, 20000);
    }

    // Form có data-loading: khóa nút khi gửi
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type=submit], button:not([type])');
            if (btn) { btn.disabled = true; btn.textContent = 'Đang xử lý...'; }
        });
    });
})();
</script>
@stack('scripts')
</body>
</html>