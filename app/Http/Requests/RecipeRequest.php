<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'cook_time'   => ['required', 'integer', 'min:1'],
            'servings'    => ['required', 'integer', 'min:1'],
            'difficulty'  => ['required', 'in:easy,medium,hard'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            'ingredients'            => ['required', 'array', 'min:1'],
            'ingredients.*.name'     => ['required', 'string', 'max:255'],
            'ingredients.*.quantity' => ['nullable', 'string', 'max:100'],

            'steps'   => ['required', 'array', 'min:1'],
            'steps.*' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'              => 'Tên món ăn không được để trống.',
            'category_id.required'        => 'Vui lòng chọn danh mục.',
            'cook_time.required'          => 'Vui lòng nhập thời gian nấu.',
            'servings.required'           => 'Vui lòng nhập số khẩu phần.',
            'image.image'                 => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes'                 => 'Ảnh phải có định dạng jpg, jpeg hoặc png.',
            'image.max'                   => 'Ảnh tối đa 2MB.',
            'ingredients.required'        => 'Cần ít nhất 1 nguyên liệu.',
            'ingredients.*.name.required' => 'Tên nguyên liệu không được để trống.',
            'steps.required'              => 'Cần ít nhất 1 bước thực hiện.',
            'steps.*.required'            => 'Nội dung bước không được để trống.',
        ];
    }
}