<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()->latest()->get();

        return $this->success($categories);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            // CHỈ LẤY DATA ĐÃ VALIDATE
            $data = $request->validated();

            // TỰ SINH SLUG (KHUYÊN DÙNG)
            $data['slug_category'] = Str::slug($data['category_name']);

            // STATUS MẶC ĐỊNH
            $data['status'] = $data['status'] ?? 'active';

            // XỬ LÝ IMAGE (CHỈ LƯU PATH)
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create($data);

            return $this->success($category, 'Tạo danh mục thành công', 201);

        } catch (\Exception $e) {
            Log::error('Create category failed', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Server error', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            // DỮ LIỆU HỢP LỆ DUY NHẤT
            $data = $request->validated();

            $data['slug_category'] = Str::slug($data['category_name']);
            
            // XỬ LÝ IMAGE
            if ($request->hasFile('image')) {

                // xoá ảnh cũ
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                // lưu ảnh mới (CHỈ LƯU PATH)
                $data['image'] = $request->file('image')->store('categories', 'public');
            }

            // UPDATE
            $category->update($data);

            return $this->success(
                $category->refresh(),
                'Cập nhật danh mục thành công'
            );

        } catch (\Exception $e) {
            Log::error('Update category failed', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Server error', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
