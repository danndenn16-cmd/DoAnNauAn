@extends('layouts.site')
@section('title', 'Trang chủ')

@section('content')
<form method="GET" action="{{ route('home') }}" class="row g-2 mb-4">
    <div class="col-md-6">
        <input type="text" name="q" value="{{ request('q') }}"
               class="form-control" placeholder="Tìm tên món ăn...">
    </div>
    <div class="col-md-4">
        <select name="category" class="form-select">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category') == $c->id)>
                    {{ $c->name }} ({{ $c->recipes_count }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button class="btn btn-success">Tìm kiếm</button>
    </div>
</form>

<div class="row g-4">
    @forelse($recipes as $r)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $r->image_url }}" class="card-img-top" alt="{{ $r->title }}">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary align-self-start mb-2">{{ $r->category->name }}</span>
                    <h5 class="card-title">
                        <a class="text-decoration-none" href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a>
                    </h5>
                    <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($r->description, 90) }}</p>
                    <div class="mt-auto small text-muted">
                        ⏱ {{ $r->cook_time }} phút · 👤 {{ $r->user->name }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Không tìm thấy công thức nào.</div>
    @endforelse
</div>

<div class="mt-4">{{ $recipes->links('pagination::bootstrap-5') }}</div>
@endsection