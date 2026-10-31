<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Tự tạo slug từ tên trước khi kiểm tra
    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->name ?? '')]);
    }

    public function rules(): array
    {
        // Khi sửa, bỏ qua chính bản ghi đang sửa để không báo trùng
        $id = $this->route('category')?->id;

        return [
            'name'        => ['required', 'string', 'max:150', 'unique:categories,name,' . $id],
            'slug'        => ['required', 'unique:categories,slug,' . $id],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Tên danh mục không được để trống.',
            'name.max'           => 'Tên danh mục tối đa 150 ký tự.',
            'name.unique'        => 'Tên danh mục đã tồn tại.',
            'slug.required'      => 'Tên danh mục phải có ít nhất một chữ cái hoặc chữ số.',
            'slug.unique'        => 'Tên này quá giống một danh mục đã có (khác nhau chỉ ở dấu).',
            'description.max'    => 'Mô tả tối đa 500 ký tự.',
        ];
    }
}
