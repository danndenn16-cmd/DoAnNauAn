<div class="dm-list" id="dmList">
    @forelse($messages as $m)
        @php $mine = $m->isMineFor($viewer); @endphp
        <div class="dm {{ $mine ? 'dm--mine' : 'dm--other' }}" data-id="{{ $m->id }}">
            <div class="dm__bubble">{{ $m->body }}</div>
            <div class="dm__meta">{{ $mine ? '' : $otherLabel . ' · ' }}{{ $m->created_at->format('H:i d/m') }}</div>
        </div>
    @empty
        <div class="dm-empty" id="dmEmpty">{{ $emptyText }}</div>
    @endforelse
</div>

<form class="dm-form" id="dmForm" autocomplete="off">
    <input type="text" id="dmInput" class="form-control" maxlength="1000" placeholder="Nhập tin nhắn..." required>
    <button class="btn btn-orange" id="dmSend">Gửi</button>
</form>

@push('scripts')
<script>
(function () {
    const list = document.getElementById('dmList');
    const form = document.getElementById('dmForm');
    const input = document.getElementById('dmInput');
    const send = document.getElementById('dmSend');
    const pollUrl = @json($pollUrl);
    const storeUrl = @json($storeUrl);
    const otherLabel = @json($otherLabel);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    let lastId = Number(@json($messages->max('id') ?? 0));
    const seen = new Set([...list.querySelectorAll('.dm')].map(el => Number(el.dataset.id)));

    const toBottom = () => { list.scrollTop = list.scrollHeight; };
    toBottom();

    // Thêm một tin vào khung (dùng textContent để chống chèn mã HTML)
    function append(m) {
        if (seen.has(m.id)) return;
        seen.add(m.id);

        const empty = document.getElementById('dmEmpty');
        if (empty) empty.remove();

        const wrap = document.createElement('div');
        wrap.className = 'dm ' + (m.mine ? 'dm--mine' : 'dm--other');
        wrap.dataset.id = m.id;

        const bubble = document.createElement('div');
        bubble.className = 'dm__bubble';
        bubble.textContent = m.body;

        const meta = document.createElement('div');
        meta.className = 'dm__meta';
        meta.textContent = (m.mine ? '' : otherLabel + ' · ') + m.time;

        wrap.append(bubble, meta);
        list.appendChild(wrap);
        lastId = Math.max(lastId, m.id);
    }

    // Cứ 4 giây hỏi máy chủ xem có tin mới không
    async function poll() {
        if (document.hidden) return;
        try {
            const res = await fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.messages.length) { data.messages.forEach(append); toBottom(); }
        } catch (e) { /* mất mạng tạm thời: bỏ qua, lần sau thử lại */ }
    }
    setInterval(poll, 4000);

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        send.disabled = true;
        try {
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ body: text })
            });
            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                showToast(res.status === 429 ? 'Bạn gửi hơi nhanh, đợi một chút nhé.' : (data.message || 'Không gửi được tin nhắn.'), 'error');
                return;
            }
            append(data.message);
            toBottom();
            input.value = '';
        } catch (err) {
            showToast('Mất kết nối, tin nhắn chưa được gửi.', 'error');
        } finally {
            send.disabled = false;
            input.focus();
        }
    });
})();
</script>
@endpush