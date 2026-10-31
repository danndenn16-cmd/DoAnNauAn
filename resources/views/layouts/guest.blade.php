<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
</head>

<body>
    <div class="auth-wrap">
        <aside class="auth-side">
            <a href="{{ route('home') }}" class="brand-pill brand-pill--light"><i></i>Cook cùng 1045</a>
            <div>
                <h2>Nấu ngon,<br>chia sẻ<br>nhiều hơn.</h2>
                <p class="mt-3 mb-0" style="opacity:.85;max-width:34ch">
                    Nơi mọi người cùng lưu giữ và chia sẻ những công thức nấu ăn yêu thích.
                </p>
            </div>
            <div class="label-mono" style="color:rgba(255,255,255,.85)">© HUIT · Đồ án Lập trình mã nguồn mở</div>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                <a href="{{ route('home') }}" class="brand-pill auth-mobile-brand"><i></i>Cook cùng 1045</a>
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>