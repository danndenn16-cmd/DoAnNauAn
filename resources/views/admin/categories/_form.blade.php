<div class="mb-3">
    <label class="form-label">Tên danh mục</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $category->name ?? '') }}" maxlength="150">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Mô tả</label>
    <textarea name="description" rows="3" maxlength="500"
        class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>