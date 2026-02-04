<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = (int) $this->route('category');
        return [
            'category_name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'category_name')->ignore($categoryId),
            ],
            // 'slug_category' => [
            //     'sometimes',
            //     'string',
            //     'max:255',
            //     Rule::unique('categories', 'slug_category')->ignore($categoryId),
            // ],
            'description' => 'nullable|string',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'sometimes|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => 'Tên danh mục là bắt buộc.',
            'category_name.string' => 'Tên danh mục phải là chuỗi.',
            'category_name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'category_name.unique' => 'Tên danh mục đã tồn tại.',

            // 'slug_category.required' => 'Slug danh mục là bắt buộc.',
            // 'slug_category.string' => 'Slug danh mục phải là chuỗi.',
            // 'slug_category.max' => 'Slug danh mục không được vượt quá 255 ký tự.',
            // 'slug_category.unique' => 'Slug danh mục đã tồn tại.',

            'description.string' => 'Mô tả phải là chuỗi.',

            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg hoặc gif.',
            'image.max' => 'Kích thước hình ảnh tối đa là 5MB.',

            'status.in'   => 'Trạng thái chỉ chấp nhận active hoặc inactive.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Cập nhật danh mục thất bại !',
            'errors' => $validator->errors(),
        ], 422));
    }
}
