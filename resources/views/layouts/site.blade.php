<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chia sẻ công thức nấu ăn')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">🍳 Công thức nấu ăn</a>

        <div class="ms-auto d-flex gap-2 align-items-center">
            @auth
                <a class="btn btn-light btn-sm" href="{{ route('recipes.create') }}">+ Đăng công thức</a>
                <a class="btn btn-outline-light btn-sm" href="{{ route('recipes.mine') }}">Công thức của tôi</a>
                <a class="btn btn-outline-light btn-sm" href="{{ route('favorites.index') }}">♥ Yêu thích</a>
                @if(auth()->user()->isAdmin())
                    <a class="btn btn-dark btn-sm" href="{{ route('admin.dashboard') }}">Quản trị</a>
                @endif
                <span class="navbar-text text-white mx-2">Xin chào, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-warning btn-sm">Đăng xuất</button>
                </form>
            @else
                <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                <a class="btn btn-light btn-sm" href="{{ route('register') }}">Đăng ký</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container mb-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>

<footer class="text-center text-muted small py-3 border-top">
    © HUIT – Đồ án Lập trình mã nguồn mở
</footer>

@stack('scripts')
</body>
</html>