<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chia sẻ công thức nấu ăn') · Cook cùng 1045</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body id="top" data-suggest-url="{{ route('recipes.suggest') }}">

{{-- Thanh thông báo phía trên --}}
<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center gap-3">
        <span>🍳 Hôm nay nấu gì? Khám phá <strong>{{ $siteStats['recipes'] }}</strong> công thức từ cộng đồng</span>
        <a href="{{ route('recipes.random') }}" class="d-none d-md-inline">🎲 Gợi ý món ngẫu nhiên →</a>
    </div>
</div>

<nav class="site-nav navbar navbar-expand-lg">
    <div class="container">
        <a class="brand-pill" href="{{ route('home') }}"><i></i>Cook cùng 1045</a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-lg-3 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') && !request('category') ? 'is-active' : '' }}"
                       href="{{ route('home') }}">Trang chủ</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request('category') ? 'is-active' : '' }}" href="#"
                       data-bs-toggle="dropdown" aria-expanded="false">Danh mục</a>
                    <ul class="dropdown-menu">
                        @foreach($navCategories as $nc)
                            <li><a class="dropdown-item" href="{{ route('home', ['category' => $nc->id]) }}">{{ $nc->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('recipes.random') }}">🎲 Ngẫu nhiên</a>
                </li>
            </ul>

            <form action="{{ route('home') }}" method="GET" class="nav-search ms-lg-auto me-lg-3 my-3 my-lg-0">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Tìm món hoặc nguyên liệu..."
                       aria-label="Tìm món ăn" autocomplete="off" data-suggest>
            </form>

            <ul class="navbar-nav align-items-lg-center gap-lg-2">
                @auth
    @php $unreadCount = auth()->user()->unreadMessagesCount(); @endphp
    <li class="nav-item">
        <a class="btn btn-orange btn-sm" href="{{ route('recipes.create') }}">+ Đăng công thức</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
            {{ auth()->user()->name }}
            <span class="msg-badge" data-msg-badge @if($unreadCount < 1) hidden @endif>{{ $unreadCount }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('recipes.mine') }}">Công thức của tôi</a></li>
            <li><a class="dropdown-item" href="{{ route('favorites.index') }}">♥ Yêu thích</a></li>
            <li>
                <a class="dropdown-item d-flex justify-content-between align-items-center"
                   href="{{ auth()->user()->isAdmin() ? route('admin.messages.index') : route('messages.index') }}">
                    {{ auth()->user()->isAdmin() ? 'Tin nhắn thành viên' : 'Nhắn tin với admin' }}
                    <span class="msg-badge" data-msg-badge @if($unreadCount < 1) hidden @endif>{{ $unreadCount }}</span>
                </a>
            </li>
            @if(auth()->user()->isStaff())
                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
            @endif
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Hồ sơ cá nhân</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item">Đăng xuất</button>
                </form>
            </li>
        </ul>
    </li>
@else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li class="nav-item"><a class="btn btn-orange btn-sm" href="{{ route('register') }}">Đăng ký</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Khu vực chứa các thông báo dạng toast --}}
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastStack" style="z-index:2000;margin-top:3.5rem"></div>

{{-- Khu vực toàn chiều rộng (dải hero của trang chủ) --}}
@yield('hero')

<main class="container">
    @yield('content')
</main>

{{-- Dải kêu gọi (ẩn ở khu Admin) --}}
@unless(request()->routeIs('admin.*'))
    <section class="cta-band">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3>Bạn có công thức gia truyền?</h3>
                <p class="mb-0">Chia sẻ để mọi người cùng nấu thử và gửi lời cảm ơn.</p>
            </div>
            @auth
                <a class="btn btn-white" href="{{ route('recipes.create') }}">+ Đăng công thức</a>
            @else
                <a class="btn btn-white" href="{{ route('register') }}">Tham gia ngay</a>
            @endauth
        </div>
    </section>
@endunless

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="brand-pill" href="{{ route('home') }}"><i></i>Cook cùng 1045</a>
                <p class="text-muted mt-3 mb-0" style="max-width:36ch">
                    Nơi lưu giữ và chia sẻ những công thức nấu ăn từ cộng đồng, từ bữa cơm gia đình đến món tráng miệng.
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <div class="label-mono mb-2">Khám phá</div>
                <ul class="footer-list">
                    <li><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li><a href="{{ route('recipes.random') }}">Món ngẫu nhiên</a></li>
                    @foreach($navCategories->take(4) as $nc)
                        <li><a href="{{ route('home', ['category' => $nc->id]) }}">{{ $nc->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <div class="label-mono mb-2">Tài khoản</div>
                <ul class="footer-list">
                    @auth
                        <li><a href="{{ route('recipes.create') }}">Đăng công thức</a></li>
                        <li><a href="{{ route('recipes.mine') }}">Công thức của tôi</a></li>
                        <li><a href="{{ route('favorites.index') }}">Yêu thích</a></li>
                        <li><a href="{{ route('profile.edit') }}">Hồ sơ cá nhân</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                        <li><a href="{{ route('register') }}">Đăng ký</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-4">
                <div class="label-mono mb-2">Cộng đồng</div>
                <div class="row g-3">
                    <div class="col-4 footer-stat"><strong>{{ $siteStats['recipes'] }}</strong><span class="label-mono">công thức</span></div>
                    <div class="col-4 footer-stat"><strong>{{ $siteStats['categories'] }}</strong><span class="label-mono">danh mục</span></div>
                    <div class="col-4 footer-stat"><strong>{{ $siteStats['members'] }}</strong><span class="label-mono">thành viên</span></div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span class="label-mono">© HUIT · Đồ án Lập trình mã nguồn mở</span>
            <a href="#top" class="label-mono">Về đầu trang ↑</a>
        </div>
    </div>
</footer>

<button type="button" class="to-top" id="toTop" aria-label="Lên đầu trang">↑</button>

@php
    // Gom thông báo từ session để hiện dạng toast
    $toasts = [];
    if (session('success')) { $toasts[] = ['success', session('success')]; }
    if (session('error'))   { $toasts[] = ['error', session('error')]; }
    if ($errors->any())     { $toasts[] = ['error', 'Vui lòng kiểm tra lại thông tin đã nhập.']; }
@endphp

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // ===== Đổ bóng menu và nút lên đầu trang =====
    const nav = document.querySelector('.site-nav');
    const toTop = document.getElementById('toTop');
    const onScroll = () => {
        nav.classList.toggle('is-scrolled', window.scrollY > 8);
        toTop.classList.toggle('is-visible', window.scrollY > 400);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // ===== Toast: gọi bằng showToast('nội dung', 'success' | 'error' | 'info') =====
    window.showToast = function (message, type) {
        type = type || 'success';
        const icons = { success: '✓', error: '⚠', info: 'ℹ' };
        const el = document.createElement('div');
        el.className = 'toast app-toast app-toast--' + type;
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        el.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
        el.setAttribute('aria-atomic', 'true');
        el.innerHTML =
            '<div class="d-flex align-items-start gap-2 p-3">' +
            '<span class="app-toast__icon"></span>' +
            '<div class="toast-body p-0 flex-grow-1"></div>' +
            '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Đóng"></button>' +
            '</div>';
        // textContent giúp chống chèn mã HTML độc hại
        el.querySelector('.app-toast__icon').textContent = icons[type] || icons.info;
        el.querySelector('.toast-body').textContent = message;
        document.getElementById('toastStack').appendChild(el);
        el.addEventListener('hidden.bs.toast', () => el.remove());
        new bootstrap.Toast(el, { delay: type === 'error' ? 7000 : 4000 }).show();
    };

    // Hiện các thông báo mà server gửi kèm theo trang
    @json($toasts).forEach(function (t) { window.showToast(t[1], t[0]); });

    // ===== Form có data-loading: khóa nút khi gửi để tránh bấm 2 lần =====
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type=submit], button:not([type])');
            if (btn) { btn.disabled = true; btn.textContent = 'Đang xử lý...'; }
        });
    });

    // ===== Gợi ý tìm kiếm tức thì (AJAX) cho mọi ô có thuộc tính data-suggest =====
    const suggestUrl = document.body.dataset.suggestUrl;
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    document.querySelectorAll('input[data-suggest]').forEach(function (input) {
        // Bọc ô nhập để định vị hộp gợi ý ngay bên dưới
        const wrap = document.createElement('div');
        wrap.className = 'suggest-wrap';
        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);

        const box = document.createElement('div');
        box.className = 'suggest-box';
        box.hidden = true;
        wrap.appendChild(box);

        const allUrl = input.form ? input.form.action : '/';
        let timer = null, controller = null, active = -1;

        const close = () => { box.hidden = true; active = -1; };

        function render(list, q) {
            if (!list.length) {
                box.innerHTML = '<div class="suggest-empty">Không có gợi ý cho “' + esc(q) + '”</div>';
            } else {
                box.innerHTML = list.map(r =>
                    '<a class="suggest-item" href="' + esc(r.url) + '">' +
                    '<img src="' + esc(r.image) + '" alt="">' +
                    '<span><strong>' + esc(r.title) + '</strong>' +
                    '<small>' + esc(r.category) + ' · ' + esc(r.cook_time) + ' phút</small></span></a>'
                ).join('') +
                '<a class="suggest-all" href="' + esc(allUrl) + '?q=' + encodeURIComponent(q) + '">Xem tất cả kết quả cho “' + esc(q) + '” →</a>';
            }
            box.hidden = false;
            active = -1;
        }

        input.addEventListener('input', function () {
            const q = input.value.trim();
            clearTimeout(timer);                      // chờ người dùng ngừng gõ 250ms mới gọi
            if (controller) controller.abort();       // hủy yêu cầu cũ chưa xong
            if (q.length < 2) { close(); return; }

            timer = setTimeout(function () {
                controller = new AbortController();
                fetch(suggestUrl + '?q=' + encodeURIComponent(q), {
                    signal: controller.signal,
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(json => render(json.data, q))
                    .catch(err => { if (err.name !== 'AbortError') close(); });
            }, 250);
        });

        // Điều khiển bằng bàn phím: ↑ ↓ để chọn, Enter để mở, Esc để đóng
        input.addEventListener('keydown', function (e) {
            const links = box.querySelectorAll('.suggest-item');
            if (e.key === 'Escape') { close(); return; }
            if (box.hidden || !links.length) return;

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                active = e.key === 'ArrowDown'
                    ? (active + 1) % links.length
                    : (active - 1 + links.length) % links.length;
                links.forEach((l, i) => l.classList.toggle('is-active', i === active));
            } else if (e.key === 'Enter' && active >= 0) {
                e.preventDefault();
                links[active].click();
            }
        });

        document.addEventListener('click', e => { if (!wrap.contains(e.target)) close(); });
    });
})();
</script>


@auth
<script>
(function () {
    const badges = document.querySelectorAll('[data-msg-badge]');
    const onChat = location.pathname.indexOf('/messages') !== -1;   // đang ở trang chat thì không cần toast
    let last = {{ (int) ($unreadCount ?? 0) }};

    function refresh() {
        if (document.hidden) return;
        fetch('{{ route('messages.unread') }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : null)
            .then(function (d) {
                if (!d) return;
                badges.forEach(function (b) { b.textContent = d.count; b.hidden = d.count < 1; });
                if (d.count > last && !onChat) showToast('Bạn có tin nhắn mới.', 'info');
                last = d.count;
            })
            .catch(function () {});
    }
    setInterval(refresh, 20000);
})();
</script>
@endauth
{{-- Trợ lý đầu bếp (ẩn ở khu Admin) --}}
@unless(request()->routeIs('admin.*'))
    @include('partials.chat-widget')
@endunless

@stack('scripts')
</body>
</html>