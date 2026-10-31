@extends('layouts.site')
@section('title', 'Trang chủ')

{{-- Dải món nổi bật (toàn chiều rộng) --}}
@section('hero')
@if($featured)
<div class="hero-band">
    <div class="container">
        <section class="hero">
            <a href="{{ route('recipes.show', $featured) }}" class="hero-img">
                <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}">
            </a>
            <div>
                <span class="label-mono is-orange">{{ $featured->is_featured ? 'Công thức nổi bật' : 'Mới nhất' }} · {{ $featured->category->name }}</span>
                <h1 class="hero-title">
                    <a href="{{ route('recipes.show', $featured) }}">{{ $featured->title }}</a>
                </h1>
                <div class="label-mono mb-3">
                    ⏱ {{ $featured->cook_time }} phút · {{ $featured->difficulty_label }} · {{ $featured->servings }} khẩu phần
                </div>
                <p class="hero-text">{{ \Illuminate\Support\Str::limit($featured->description, 170) }}</p>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="avatar">{{ mb_substr($featured->user->name, 0, 1) }}</span>
                    <span class="small">{{ $featured->user->name }} · {{ $featured->created_at->format('d/m/Y') }}</span>
                </div>
                <a href="{{ route('recipes.show', $featured) }}" class="btn btn-orange">Xem công thức →</a>
            </div>
        </section>
    </div>
</div>
@endif
@endsection

@section('content')

{{-- Được yêu thích --}}
@if($popular->isNotEmpty())
<div class="section-head">
    <h2>Được yêu thích</h2>
    <span class="label-mono">Top {{ $popular->count() }}</span>
</div>
<div class="popular">
    @foreach($popular as $p)
    <a href="{{ route('recipes.show', $p) }}" class="popular-item">
        <img src="{{ $p->image_url }}" alt="{{ $p->title }}" loading="lazy">
        <div>
            <span class="label-mono is-orange">{{ $p->category->name }}</span>
            <strong>{{ $p->title }}</strong>
            <span class="small text-muted">♥ {{ $p->favorited_by_count }}</span>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- Danh sách công thức --}}
<div class="section-head" id="recipes">
    <h2>
        @if(request('q'))
        Kết quả cho “{{ request('q') }}”
        @else
        Công thức mới
        @endif
    </h2>
    <span class="label-mono">{{ $recipes->total() }} món</span>
</div>

<form method="GET" action="{{ route('home') }}" class="row g-2 mb-3">
    @if(request('category'))
    <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    <div class="col-md-9">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-pill px-4"
       placeholder="Tìm tên món ăn hoặc nguyên liệu..." autocomplete="off" data-suggest>
    </div>
    <div class="col-md-3 d-grid">
        <button class="btn btn-orange">Tìm kiếm</button>
    </div>
</form>

<div class="chips mb-4">
    <a class="chip {{ request('category') ? '' : 'is-active' }}"
        href="{{ route('home', array_filter(['q' => request('q')])) }}">Tất cả</a>
    @foreach($categories as $c)
    <a class="chip {{ request('category') == $c->id ? 'is-active' : '' }}"
        href="{{ route('home', array_filter(['category' => $c->id, 'q' => request('q')])) }}">
        {{ $c->name }} <small>{{ $c->recipes_count }}</small>
    </a>
    @endforeach
</div>

<div class="row g-4">
    @forelse($recipes as $r)
    <div class="col-12 col-sm-6 col-lg-4">
        @include('recipes._card', ['r' => $r])
    </div>
    @empty
    <div class="col-12">
        <div class="empty">
            Không tìm thấy công thức nào.
            <a href="{{ route('home') }}">Xem tất cả công thức</a>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $recipes->links('pagination::bootstrap-5') }}
</div>
@endsection