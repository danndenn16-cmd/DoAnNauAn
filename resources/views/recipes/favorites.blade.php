@extends('layouts.site')
@section('title', 'Công thức yêu thích')

@section('content')
<div class="mt-4 mb-4">
    <span class="label-mono is-orange">Bộ sưu tập</span>
    <h1 class="fw-bold mb-1" style="font-size:clamp(2rem,5vw,3rem)">Công thức yêu thích</h1>
    <p class="text-muted mb-0">{{ $recipes->total() }} món đã lưu</p>
</div>

<div class="row g-4">
    @forelse($recipes as $r)
    <div class="col-12 col-sm-6 col-lg-4">
        @include('recipes._card', ['r' => $r])
    </div>
    @empty
    <div class="col-12">
        <div class="empty">
            Bạn chưa yêu thích công thức nào.
            <div><a href="{{ route('home') }}" class="btn btn-orange mt-3">Khám phá công thức</a></div>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    {{ $recipes->links('pagination::bootstrap-5') }}
</div>
@endsection