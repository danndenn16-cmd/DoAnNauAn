@extends('layouts.site')
@section('title', $recipe->title)

@section('content')

<nav class="label-mono mt-4 mb-2">
    <a href="{{ route('home') }}">Trang chủ</a> /
    <a href="{{ route('home', ['category' => $recipe->category_id]) }}">{{ $recipe->category->name }}</a>
</nav>

{{-- Phần 1: Tiêu đề + nguyên liệu --}}
<section class="row g-5 align-items-start">
    <div class="col-lg-5">
        <span class="label-mono is-orange">{{ $recipe->category->name }}</span>
        <h1 class="detail-title">{{ $recipe->title }}</h1>
        <p class="detail-lead">{{ $recipe->description }}</p>

        <div class="meta-pills">
            <span class="meta-pill">⏱ {{ $recipe->cook_time }} phút</span>
            <span class="meta-pill" id="servPill">{{ $recipe->servings }} khẩu phần</span>
            <span class="meta-pill">{{ $recipe->difficulty_label }}</span>
        </div>

        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="avatar">{{ mb_substr($recipe->user->name, 0, 1) }}</span>
            <span class="small">Đăng bởi <strong>{{ $recipe->user->name }}</strong> · {{ $recipe->created_at->format('d/m/Y') }}</span>
        </div>

        @if($recipe->status === 'pending')
            <div class="flash mb-3">Công thức này đang chờ quản trị viên duyệt.</div>
        @elseif($recipe->status === 'rejected')
            <div class="flash flash--danger mb-3">
                Công thức bị từ chối: {{ $recipe->rejection_reason }}. Hãy chỉnh sửa và gửi lại.
            </div>
        @endif

        <div class="d-flex flex-wrap gap-2">
            @auth
                <button id="fav-btn" type="button"
                        data-url="{{ route('recipes.favorite', $recipe) }}"
                        class="btn btn-sm {{ $isFavorited ? 'btn-orange' : 'btn-ink-outline' }}">
                    <span id="fav-icon">{{ $isFavorited ? '♥' : '♡' }}</span>
                    Yêu thích (<span id="fav-count">{{ $favCount }}</span>)
                </button>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-ink-outline">♡ Yêu thích ({{ $favCount }})</a>
            @endauth

            <button type="button" class="btn btn-sm btn-ink-outline"
                    data-chat-open data-chat-recipe="{{ $recipe->id }}" data-chat-recipe-title="{{ $recipe->title }}">
                🍳 Hỏi bếp trưởng về món này
            </button>

            @can('update', $recipe)
                <a href="{{ route('recipes.edit', $recipe) }}" class="btn btn-sm btn-ink-outline">Sửa</a>
            @endcan

            @can('delete', $recipe)
                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST"
                      onsubmit="return confirm('Xóa công thức này?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-ink-outline">Xóa</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="col-lg-7">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="label-mono">Nguyên liệu · {{ $recipe->ingredients->count() }}</div>

            {{-- Bộ điều chỉnh số khẩu phần --}}
            <div class="servings" data-base="{{ $recipe->servings }}">
                <span class="label-mono">Khẩu phần</span>
                <button type="button" class="servings__btn" id="servMinus" aria-label="Giảm khẩu phần">−</button>
                <output id="servValue" class="servings__value">{{ $recipe->servings }}</output>
                <button type="button" class="servings__btn" id="servPlus" aria-label="Tăng khẩu phần">+</button>
                <button type="button" class="servings__reset" id="servReset" hidden>Đặt lại</button>
            </div>
        </div>

        <div class="ing-grid">
            @foreach($recipe->ingredients as $i)
                <div class="ing">
                    <div class="ing__n">{{ $loop->iteration }}</div>
                    <div class="ing__qty" data-qty="{{ $i->quantity }}">{{ $i->quantity ?: '—' }}</div>
                    <div class="ing__name">{{ $i->name }}</div>
                </div>
            @endforeach
        </div>

        <p class="small text-muted mt-3 mb-0" id="servNote" hidden>
            Số lượng đã được tính lại cho <strong id="servNoteN"></strong> khẩu phần. Gia vị nên nêm nếm theo khẩu vị.
        </p>
    </div>
</section>

<hr class="divider">

{{-- Phần 2: Ảnh món + các bước --}}
<section class="row g-5">
    <div class="col-lg-5">
        <div class="sticky-photo">
            <div class="dish-photo">
                <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}">
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <span class="label-mono is-orange">Cách làm</span>
        <h2 class="mt-1 fw-bold">Các bước thực hiện</h2>

        <div class="steps">
            @foreach($recipe->steps as $s)
                <div class="step">
                    <span class="step__n">{{ $loop->iteration }}</span>
                    <p>{{ $s->content }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<hr class="divider">

{{-- Bình luận --}}
<section class="row">
    <div class="col-lg-8">
        <div class="label-mono is-orange">Trao đổi</div>
        <h2 class="mt-1 mb-3 fw-bold">Bình luận ({{ $comments->count() }})</h2>

        @auth
            <form action="{{ route('comments.store', $recipe) }}" method="POST" class="mb-4">
                @csrf
                <textarea name="content" rows="3"
                          class="form-control @error('content') is-invalid @enderror"
                          placeholder="Chia sẻ cảm nhận của bạn...">{{ old('content') }}</textarea>
                @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <button class="btn btn-orange btn-sm mt-2">Gửi bình luận</button>
            </form>
        @else
            <p class="text-muted">Hãy <a href="{{ route('login') }}">đăng nhập</a> để bình luận.</p>
        @endauth

        @forelse($comments as $c)
            <div class="comment">
                <span class="avatar">{{ mb_substr($c->user->name, 0, 1) }}</span>
                <div class="comment__body">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $c->user->name }}</strong>
                        <span class="label-mono">{{ $c->created_at->format('d/m/Y H:i') }}</span>
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
            </div>
        @empty
            <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
        @endforelse
    </div>
</section>

{{-- Gợi ý cùng danh mục --}}
@if($related->isNotEmpty())
    <div class="section-head">
        <h2>Có thể bạn sẽ thích</h2>
        <span class="label-mono">{{ $recipe->category->name }}</span>
    </div>
    <div class="row g-4">
        @foreach($related as $r)
            <div class="col-12 col-sm-6 col-lg-4">
                @include('recipes._card', ['r' => $r])
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
// ===== AJAX: bật/tắt yêu thích (kèm toast) =====
(function () {
    const favBtn = document.getElementById('fav-btn');
    if (!favBtn) return;

    favBtn.addEventListener('click', function () {
        fetch(favBtn.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => { if (!res.ok) throw new Error('http'); return res.json(); })
        .then(data => {
            document.getElementById('fav-count').textContent = data.count;
            document.getElementById('fav-icon').textContent = data.favorited ? '♥' : '♡';
            favBtn.classList.toggle('btn-orange', data.favorited);
            favBtn.classList.toggle('btn-ink-outline', !data.favorited);
            showToast(data.favorited ? 'Đã thêm vào danh sách yêu thích.' : 'Đã bỏ khỏi danh sách yêu thích.');
        })
        .catch(() => showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error'));
    });
})();

// ===== Điều chỉnh số khẩu phần: nhân hoặc chia số lượng nguyên liệu =====
(function () {
    const wrap = document.querySelector('.servings');
    if (!wrap) return;

    const base = parseInt(wrap.dataset.base, 10) || 1;
    let current = base;

    const out = document.getElementById('servValue');
    const minus = document.getElementById('servMinus');
    const plus = document.getElementById('servPlus');
    const reset = document.getElementById('servReset');
    const note = document.getElementById('servNote');
    const pill = document.getElementById('servPill');
    const qtyEls = document.querySelectorAll('.ing__qty[data-qty]');

    // Tách số ở đầu chuỗi: "500g", "1/2 muỗng", "1 1/2 chén", "2,5 lít"
    function parseQty(text) {
        const m = text.match(/^\s*(\d+\s+\d+\/\d+|\d+\/\d+|\d+(?:[.,]\d+)?)(.*)$/);
        if (!m) return null;                                   // "vừa đủ", "một ít" → giữ nguyên
        if (/^\s*[-–~]\s*\d/.test(m[2])) return null;          // khoảng "2-3 quả" → giữ nguyên

        const raw = m[1].trim();
        let num;
        if (raw.includes('/')) {
            const parts = raw.split(/\s+/);
            if (parts.length === 2) {                          // hỗn số: 1 1/2
                const f = parts[1].split('/');
                num = parseFloat(parts[0]) + f[0] / f[1];
            } else {                                           // phân số: 1/2
                const f = raw.split('/');
                num = f[0] / f[1];
            }
        } else {
            num = parseFloat(raw.replace(',', '.'));
        }
        return { num: num, rest: m[2] };
    }

    function fmt(n) {
        const r = Math.round(n * 100) / 100;
        return r.toLocaleString('vi-VN', { maximumFractionDigits: 2, useGrouping: false });
    }

    function update() {
        out.textContent = current;
        pill.textContent = current + ' khẩu phần';
        const factor = current / base;

        qtyEls.forEach(function (el) {
            const original = el.dataset.qty;
            const p = original ? parseQty(original) : null;
            el.textContent = !original ? '—' : (p && factor !== 1 ? fmt(p.num * factor) + p.rest : original);
            el.classList.remove('is-scaled');
            void el.offsetWidth;                               // khởi động lại hiệu ứng
            if (factor !== 1) el.classList.add('is-scaled');
        });

        reset.hidden = note.hidden = (current === base);
        document.getElementById('servNoteN').textContent = current;
        minus.disabled = current <= 1;
        plus.disabled = current >= 50;
    }

    minus.addEventListener('click', () => { if (current > 1) { current--; update(); } });
    plus.addEventListener('click', () => { if (current < 50) { current++; update(); } });
    reset.addEventListener('click', () => { current = base; update(); });
    update();
})();
</script>
@endpush