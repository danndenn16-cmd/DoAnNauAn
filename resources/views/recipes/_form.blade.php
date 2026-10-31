@php
$ingredients = old('ingredients', isset($recipe)
? $recipe->ingredients->map(fn ($i) => ['name' => $i->name, 'quantity' => $i->quantity])->toArray()
: [['name' => '', 'quantity' => '']]);

$steps = old('steps', isset($recipe) ? $recipe->steps->pluck('content')->toArray() : ['']);
$hasImage = isset($recipe) && $recipe->image_path;
@endphp

@if($errors->any())
<div class="flash flash--danger mt-0 mb-4">
    <strong>Vui lòng kiểm tra lại:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row g-4">
    {{-- Cột trái: nội dung --}}
    <div class="col-lg-8">
        <section class="form-card">
            <h2>Thông tin chính</h2>

            <div class="mb-3">
                <label class="form-label label-mono" for="title">Tên món ăn</label>
                <input id="title" type="text" name="title" maxlength="255"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $recipe->title ?? '') }}" placeholder="Ví dụ: Thịt kho trứng">
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label label-mono" for="description">Mô tả ngắn</label>
                <textarea id="description" name="description" rows="4"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="Giới thiệu đôi nét về món ăn...">{{ old('description', $recipe->description ?? '') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </section>

        <section class="form-card">
            <h2>Nguyên liệu</h2>
            <div class="dyn-list" id="ingredient-list">
                @foreach($ingredients as $idx => $ing)
                <div class="dyn-row">
                    <span class="num"></span>
                    <div class="grow">
                        <input type="text" name="ingredients[{{ $idx }}][name]" class="form-control"
                            placeholder="Tên nguyên liệu" value="{{ $ing['name'] ?? '' }}">
                        @error('ingredients.' . $idx . '.name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="text" name="ingredients[{{ $idx }}][quantity]" class="form-control qty"
                        placeholder="Số lượng" value="{{ $ing['quantity'] ?? '' }}">
                    <button type="button" class="btn-icon" onclick="removeRow(this)" aria-label="Xóa dòng">✕</button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-ink-outline btn-sm mt-2" onclick="addIngredient()">+ Thêm nguyên liệu</button>
            @error('ingredients') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
        </section>

        <section class="form-card">
            <h2>Các bước thực hiện</h2>
            <div class="dyn-list" id="step-list">
                @foreach($steps as $k => $content)
                <div class="dyn-row">
                    <span class="num"></span>
                    <div class="grow">
                        <textarea name="steps[]" rows="2" class="form-control"
                            placeholder="Mô tả bước thực hiện">{{ $content }}</textarea>
                        @error('steps.' . $k)
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="button" class="btn-icon" onclick="removeRow(this)" aria-label="Xóa bước">✕</button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-ink-outline btn-sm mt-2" onclick="addStep()">+ Thêm bước</button>
            @error('steps') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
        </section>
    </div>

    {{-- Cột phải: ảnh + chi tiết + nút --}}
    <div class="col-lg-4">
        <div class="sticky-side">
            <section class="form-card">
                <h2>Ảnh món ăn</h2>
                <label class="upload-box {{ $hasImage ? 'has-image' : '' }}" id="uploadBox" for="image">
                    <img id="imgPreview" src="{{ $hasImage ? $recipe->image_url : '' }}" alt="Ảnh xem trước">
                    <span class="upload-box__hint">
                        <span style="font-size:2rem">📷</span>
                        <strong>Bấm để chọn ảnh</strong>
                        <span>JPG, PNG · tối đa 2MB</span>
                    </span>
                </label>
                <input type="file" id="image" name="image" class="d-none" accept=".jpg,.jpeg,.png">
                @error('image') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                @if($hasImage)
                <div class="small text-muted mt-2">Chọn ảnh mới nếu muốn thay ảnh hiện tại.</div>
                @endif
            </section>

            <section class="form-card">
                <h2>Chi tiết</h2>

                <div class="mb-3">
                    <label class="form-label label-mono" for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $recipe->category_id ?? '') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label label-mono" for="difficulty">Độ khó</label>
                    <select id="difficulty" name="difficulty" class="form-select">
                        @foreach(['easy' => 'Dễ', 'medium' => 'Trung bình', 'hard' => 'Khó'] as $k => $v)
                        <option value="{{ $k }}" @selected(old('difficulty', $recipe->difficulty ?? 'easy') == $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label label-mono" for="cook_time">Thời gian (phút)</label>
                        <input id="cook_time" type="number" min="1" name="cook_time"
                            class="form-control @error('cook_time') is-invalid @enderror"
                            value="{{ old('cook_time', $recipe->cook_time ?? 30) }}">
                        @error('cook_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label label-mono" for="servings">Khẩu phần</label>
                        <input id="servings" type="number" min="1" name="servings"
                            class="form-control @error('servings') is-invalid @enderror"
                            value="{{ old('servings', $recipe->servings ?? 2) }}">
                        @error('servings') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </section>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-orange py-2">{{ $submitLabel }}</button>
                <a href="{{ $cancelUrl }}" class="btn btn-ink-outline">Hủy</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowN = 1000;

    function removeRow(btn) {
        const list = btn.closest('.dyn-list');
        const rows = list.querySelectorAll('.dyn-row');
        if (rows.length <= 1) {
            // Luôn giữ lại ít nhất một dòng: chỉ xóa nội dung
            rows[0].querySelectorAll('input, textarea').forEach(i => i.value = '');
            return;
        }
        btn.closest('.dyn-row').remove();
    }

    function addIngredient() {
        rowN++;
        document.getElementById('ingredient-list').insertAdjacentHTML('beforeend', `
            <div class="dyn-row">
                <span class="num"></span>
                <div class="grow"><input type="text" name="ingredients[${rowN}][name]" class="form-control" placeholder="Tên nguyên liệu"></div>
                <input type="text" name="ingredients[${rowN}][quantity]" class="form-control qty" placeholder="Số lượng">
                <button type="button" class="btn-icon" onclick="removeRow(this)" aria-label="Xóa dòng">✕</button>
            </div>`);
    }

    function addStep() {
        document.getElementById('step-list').insertAdjacentHTML('beforeend', `
            <div class="dyn-row">
                <span class="num"></span>
                <div class="grow"><textarea name="steps[]" rows="2" class="form-control" placeholder="Mô tả bước thực hiện"></textarea></div>
                <button type="button" class="btn-icon" onclick="removeRow(this)" aria-label="Xóa bước">✕</button>
            </div>`);
    }

    // Xem trước ảnh khi chọn tệp
    const fileInput = document.getElementById('image');
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Ảnh tối đa 2MB, vui lòng chọn ảnh khác.');
            fileInput.value = '';
            return;
        }
        document.getElementById('imgPreview').src = URL.createObjectURL(file);
        document.getElementById('uploadBox').classList.add('has-image');
    });
</script>
@endpush