@php
    $ingredients = old('ingredients', isset($recipe)
        ? $recipe->ingredients->map(fn ($i) => ['name' => $i->name, 'quantity' => $i->quantity])->toArray()
        : [['name' => '', 'quantity' => '']]);

    $steps = old('steps', isset($recipe) ? $recipe->steps->pluck('content')->toArray() : ['']);
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Tên món ăn</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $recipe->title ?? '') }}">
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Danh mục</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">-- Chọn danh mục --</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $recipe->category_id ?? '') == $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Độ khó</label>
        <select name="difficulty" class="form-select">
            @foreach(['easy' => 'Dễ', 'medium' => 'Trung bình', 'hard' => 'Khó'] as $k => $v)
                <option value="{{ $k }}" @selected(old('difficulty', $recipe->difficulty ?? 'easy') == $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Thời gian nấu (phút)</label>
        <input type="number" name="cook_time" class="form-control @error('cook_time') is-invalid @enderror"
               value="{{ old('cook_time', $recipe->cook_time ?? 30) }}">
        @error('cook_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Số khẩu phần</label>
        <input type="number" name="servings" class="form-control @error('servings') is-invalid @enderror"
               value="{{ old('servings', $recipe->servings ?? 2) }}">
        @error('servings') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Mô tả ngắn</label>
    <textarea name="description" rows="3" class="form-control">{{ old('description', $recipe->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Ảnh món ăn (jpg, png, tối đa 2MB)</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png">
    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @if(isset($recipe) && $recipe->image_path)
        <img src="{{ $recipe->image_url }}" alt="" class="mt-2 rounded" style="max-height:120px">
    @endif
</div>

<h5 class="mt-4">Nguyên liệu</h5>
<div id="ingredient-list">
    @foreach($ingredients as $idx => $ing)
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <input type="text" name="ingredients[{{ $idx }}][name]" class="form-control"
                       placeholder="Tên nguyên liệu" value="{{ $ing['name'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="ingredients[{{ $idx }}][quantity]" class="form-control"
                       placeholder="Số lượng (vd: 200g)" value="{{ $ing['quantity'] ?? '' }}">
            </div>
            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.row').remove()">Xóa</button>
            </div>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-outline-success btn-sm mb-3" onclick="addIngredient()">+ Thêm nguyên liệu</button>

<h5 class="mt-4">Các bước thực hiện</h5>
<div id="step-list">
    @foreach($steps as $content)
        <div class="row g-2 mb-2">
            <div class="col-md-10">
                <textarea name="steps[]" rows="2" class="form-control" placeholder="Nội dung bước">{{ $content }}</textarea>
            </div>
            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.row').remove()">Xóa</button>
            </div>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-outline-success btn-sm mb-3" onclick="addStep()">+ Thêm bước</button>

@push('scripts')
<script>
    let ingN = 1000;

    function addIngredient() {
        ingN++;
        document.getElementById('ingredient-list').insertAdjacentHTML('beforeend', `
            <div class="row g-2 mb-2">
                <div class="col-md-6"><input type="text" name="ingredients[${ingN}][name]" class="form-control" placeholder="Tên nguyên liệu"></div>
                <div class="col-md-4"><input type="text" name="ingredients[${ingN}][quantity]" class="form-control" placeholder="Số lượng (vd: 200g)"></div>
                <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.row').remove()">Xóa</button></div>
            </div>`);
    }

    function addStep() {
        document.getElementById('step-list').insertAdjacentHTML('beforeend', `
            <div class="row g-2 mb-2">
                <div class="col-md-10"><textarea name="steps[]" rows="2" class="form-control" placeholder="Nội dung bước"></textarea></div>
                <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.row').remove()">Xóa</button></div>
            </div>`);
    }
</script>
@endpush