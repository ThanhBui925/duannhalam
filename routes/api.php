<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CategoryController,
};


Route::prefix('categories')->controller(CategoryController::class)->group(function () {
    // Route::get('/trashed', 'trashed');
    // Route::post('{id}/restore', 'restore');
    // Route::delete('{id}/force-delete', 'forceDelete');
    // Route::get('/get-list', 'getCategoryActive');
    Route::apiResource('/', CategoryController::class)->parameter('', 'category');
});
