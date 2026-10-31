<button type="button" class="chat-fab" id="chatFab" data-bs-toggle="offcanvas" data-bs-target="#chatPanel"
        aria-controls="chatPanel" aria-label="Mở trợ lý đầu bếp">
    <span>🍳</span><span class="chat-fab__label">Hỏi bếp trưởng</span>
</button>

<div class="offcanvas offcanvas-end chat-panel" tabindex="-1" id="chatPanel"
     aria-labelledby="chatTitle" data-url="{{ route('chat.ask') }}">
    <div class="offcanvas-header">
        <div>
            <div class="label-mono is-orange">Trợ lý AI</div>
            <h5 class="offcanvas-title fw-bold mb-0" id="chatTitle">Bếp trưởng ảo</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>

    <div class="chat-context" id="chatContext" hidden>
        <span>Đang hỏi về món: <strong id="chatContextTitle"></strong></span>
        <button type="button" id="chatContextClear" aria-label="Bỏ ngữ cảnh món">✕</button>
    </div>

    <div class="offcanvas-body chat-body" id="chatBody" aria-live="polite">
        <div class="chat-msg chat-msg--bot">
            <div class="chat-bubble">
                Xin chào! Mình là bếp trưởng ảo của Cook cùng 1045 🍳<br>
                Bạn cứ hỏi về món ăn, nguyên liệu hay mẹo nấu nhé.
            </div>
        </div>
        <div class="chat-suggest" id="chatSuggest">
            <button type="button">Mình có trứng và cà chua, nấu món gì?</button>
            <button type="button">Gợi ý món nấu nhanh dưới 20 phút</button>
            <button type="button">Mẹo nấu cơm ngon, tơi xốp</button>
            <button type="button">Gợi ý món chay</button>
        </div>
    </div>

    <form class="chat-form" id="chatForm" autocomplete="off">
        <input type="text" class="form-control" id="chatInput" maxlength="500" placeholder="Nhập câu hỏi..." required>
        <button class="btn btn-orange" id="chatSend">Gửi</button>
    </form>
</div>

<script>
(function () {
    const panel = document.getElementById('chatPanel');
    const body = document.getElementById('chatBody');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const send = document.getElementById('chatSend');
    const ctxBox = document.getElementById('chatContext');
    const ctxTitle = document.getElementById('chatContextTitle');
    const url = panel.dataset.url;

    let history = [];      // các lượt hỏi đáp gần nhất, gửi kèm để trợ lý nhớ mạch trò chuyện
    let recipeId = null;   // món đang được hỏi (nếu có)
    let busy = false;

    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // Định dạng đơn giản: escape trước (chống chèn mã), sau đó đổi **đậm**, gạch đầu dòng, xuống dòng
    function format(text) {
        return esc(text)
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/^[ \t]*[*\-][ \t]+/gm, '• ')
            .replace(/\n/g, '<br>');
    }

    function addMessage(role, html, links) {
        const wrap = document.createElement('div');
        wrap.className = 'chat-msg chat-msg--' + role;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.innerHTML = html;
        wrap.appendChild(bubble);

        if (links && links.length) {
            const box = document.createElement('div');
            box.className = 'chat-links';
            links.forEach(function (l) {
                const a = document.createElement('a');
                a.href = l.url;
                a.className = 'chat-link';
                a.textContent = '🍽 ' + l.title;
                box.appendChild(a);
            });
            wrap.appendChild(box);
        }

        body.appendChild(wrap);
        body.scrollTop = body.scrollHeight;
        return wrap;
    }

    async function ask(text) {
        text = text.trim();
        if (busy || !text) return;
        busy = true;
        send.disabled = true;

        const suggest = document.getElementById('chatSuggest');
        if (suggest) suggest.remove();

        addMessage('user', esc(text));
        const typing = addMessage('bot', '<span class="typing"><i></i><i></i><i></i></span>');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: text, history: history, recipe_id: recipeId })
            });
            const data = await res.json().catch(() => ({}));
            typing.remove();

            if (!res.ok) {
                const msg = res.status === 429
                    ? 'Bạn hỏi hơi nhanh, đợi một chút rồi hỏi tiếp nhé.'
                    : (data.message || 'Trợ lý đang bận, bạn thử lại sau nhé.');
                addMessage('bot', esc(msg));
                return;
            }

            addMessage('bot', format(data.reply), data.recipes);
            history.push({ role: 'user', text: text }, { role: 'model', text: data.reply });
            history = history.slice(-8);
        } catch (e) {
            typing.remove();
            addMessage('bot', 'Không kết nối được tới máy chủ. Bạn kiểm tra mạng rồi thử lại nhé.');
            showToast('Không kết nối được tới trợ lý.', 'error');
        } finally {
            busy = false;
            send.disabled = false;
            input.focus();
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const t = input.value;
        input.value = '';
        ask(t);
    });

    document.querySelectorAll('#chatSuggest button').forEach(function (b) {
        b.addEventListener('click', () => ask(b.textContent));
    });

    // Nút "Hỏi bếp trưởng về món này" ở trang chi tiết
    document.querySelectorAll('[data-chat-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            recipeId = btn.dataset.chatRecipe || null;
            ctxTitle.textContent = btn.dataset.chatRecipeTitle || '';
            ctxBox.hidden = !recipeId;
            bootstrap.Offcanvas.getOrCreateInstance(panel).show();
        });
    });

    document.getElementById('chatContextClear').addEventListener('click', function () {
        recipeId = null;
        ctxBox.hidden = true;
    });

    panel.addEventListener('shown.bs.offcanvas', () => input.focus());
})();
</script>