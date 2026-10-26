@extends('layouts.site')
@section('title', $recipe->title)

@section('content')
<a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm mb-3">← Quay lại</a>

<div class="card shadow-sm">
    <img src="{{ $recipe->image_url }}" class="card-img-top" alt="{{ $recipe->title }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <h2>{{ $recipe->title }}</h2>
            <div class="d-flex gap-2">
                @auth
                    <button id="fav-btn" type="button"
                            data-url="{{ route('recipes.favorite', $recipe) }}"
                            class="btn btn-sm {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }}">
                        <span id="fav-icon">{{ $isFavorited ? '♥' : '♡' }}</span>
                        Yêu thích (<span id="fav-count">{{ $favCount }}</span>)
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger">
                        ♡ Yêu thích ({{ $favCount }})
                    </a>
                @endauth

                @can('update', $recipe)
                    <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-sm btn-primary">Sửa</a>
                @endcan
            </div>
        </div>

        <p class="text-muted">
            {{ $recipe->category->name }} · ⏱ {{ $recipe->cook_time }} phút ·
            {{ $recipe->servings }} khẩu phần · Đăng bởi {{ $recipe->user->name }}
        </p>
        <p>{{ $recipe->description }}</p>

        <h4 class="mt-4">Nguyên liệu</h4>
        <ul>
            @foreach($recipe->ingredients as $i)
                <li>{{ $i->name }} - {{ $i->quantity }}</li>
            @endforeach
        </ul>

        <h4 class="mt-4">Các bước thực hiện</h4>
        <ol>
            @foreach($recipe->steps as $s)
                <li class="mb-2">{{ $s->content }}</li>
            @endforeach
        </ol>
    </div>
</div>

{{-- Bình luận --}}
<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h4>Bình luận ({{ $comments->count() }})</h4>

        @auth
            <form action="{{ route('comments.store', $recipe) }}" method="POST" class="mb-4">
                @csrf
                <textarea name="content" rows="3"
                          class="form-control @error('content') is-invalid @enderror"
                          placeholder="Chia sẻ cảm nhận của bạn...">{{ old('content') }}</textarea>
                @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <button class="btn btn-success btn-sm mt-2">Gửi bình luận</button>
            </form>
        @else
            <p class="text-muted">Hãy <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</p>
        @endauth

        @forelse($comments as $c)
            <div class="border-top py-2">
                <div class="d-flex justify-content-between">
                    <strong>{{ $c->user->name }}</strong>
                    <small class="text-muted">{{ $c->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div>{{ $c->content }}</div>

                @auth
                    @if(auth()->id() === $c->user_id || auth()->user()->isAdmin())
                        <form action="{{ route('comments.destroy', $c) }}" method="POST"
                              onsubmit="return confirm('Xóa bình luận này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-link btn-sm text-danger p-0">Xóa</button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <p class="text-muted mb-0">Chưa có bình luận nào.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    // AJAX: bật/tắt yêu thích mà không tải lại trang
    const favBtn = document.getElementById('fav-btn');

    if (favBtn) {
        favBtn.addEventListener('click', function () {
            fetch(favBtn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('fav-count').textContent = data.count;
                document.getElementById('fav-icon').textContent = data.favorited ? '♥' : '♡';
                favBtn.classList.toggle('btn-danger', data.favorited);
                favBtn.classList.toggle('btn-outline-danger', !data.favorited);
            })
            .catch(() => alert('Có lỗi xảy ra, vui lòng thử lại.'));
        });
    }
</script>
@endpush