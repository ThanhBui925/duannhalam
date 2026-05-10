<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ApiResponseTrait, AuthorizesRequests;
    public function index()
    {
        $products = Product::query()->latest()->get();

        return $this->success($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return $this->success($product, 'Tạo sản phẩm thành công', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = Product::where('slug', $slug)
                            ->with('category')
                            ->first();
        if (!$product) {
            return $this->error('Không tìm thấy sản phẩm', 404);
        }
        return $this->success($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            if (isset($data['name'])) {

                $baseSlug = Str::slug($data['name']);
                $slug = $baseSlug;
                $count = 1;

                while (
                    Product::where('slug', $slug)
                        ->where('id', '!=', $product->id)
                        ->exists()
                ) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                $data['slug'] = $slug;
            }

            // Upload ảnh
            if ($request->hasFile('image')) {

                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            return $this->success(
                $product->refresh(),
                'Cập nhật sản phẩm thành công'
            );

        } catch (\Exception $e) {

            Log::error('Update product failed', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Server error', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
