<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UpdateProductRequest extends FormRequest
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
        return [
            'category_id' => 'required|exists:categories,id',

            'name' => 'required|string|max:255',

            'slug' => 'nullable|string|max:255|unique:products,slug,' . $this->product->id,

            'price' => 'required|numeric|min:0',

            'sale_price' => 'nullable|numeric|min:0|lte:price',

            'quantity' => 'required|integer|min:0',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Danh mục không được để trống',
            'category_id.exists' => 'Danh mục không tồn tại',

            'name.required' => 'Tên sản phẩm không được để trống',

            'price.required' => 'Giá sản phẩm không được để trống',

            'sale_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc',

            'quantity.required' => 'Số lượng không được để trống',

             'image.image' => 'File phải là hình ảnh',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Cập nhật sản phẩm thất bại !',
            'errors' => $validator->errors(),
        ], 422));
    }
}
