@extends('layouts.site')
@section('title', 'Công thức yêu thích')

@section('content')
<h2 class="mb-3">Công thức yêu thích của tôi</h2>

<div class="row g-4">
    @forelse($recipes as $r)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $r->image_url }}" class="card-img-top" alt="{{ $r->title }}">
                <div class="card-body">
                    <span class="badge bg-secondary mb-2">{{ $r->category->name }}</span>
                    <h5 class="card-title">
                        <a class="text-decoration-none" href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a>
                    </h5>
                    <div class="small text-muted">⏱ {{ $r->cook_time }} phút · 👤 {{ $r->user->name }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Bạn chưa yêu thích công thức nào.</div>
    @endforelse
</div>

<div class="mt-4">{{ $recipes->links('pagination::bootstrap-5') }}</div>
@endsection