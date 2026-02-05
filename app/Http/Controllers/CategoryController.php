<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;
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
            $data = $request->validated();

            $data['slug_category'] = Str::slug($data['category_name']);

            $data['status'] = $data['status'] ?? 'active';

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
    public function show($slug_category)
    {
        $category = Category::where('slug_category',$slug_category)->first();
        if(!$category){
            return $this->error('Không tìm thấy danh mục', 404);
        }
        return $this->success($category, 'Xem danh mục thành công', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $data = $request->validated();

            // Chỉ tạo slug khi đổi tên
            if (isset($data['category_name'])) {

                $baseSlug = Str::slug($data['category_name']);
                $slug = $baseSlug;
                $count = 1;

                while (
                    Category::where('slug_category', $slug)
                        ->where('id', '!=', $category->id)
                        ->exists()
                ) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                $data['slug_category'] = $slug;
            }

            // Upload ảnh
            if ($request->hasFile('image')) {

                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                $data['image'] = $request->file('image')->store('categories', 'public');
            }

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
