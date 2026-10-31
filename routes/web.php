<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

// ===== Công khai =====
Route::get('/', [RecipeController::class, 'index'])->name('home');
Route::get('/random', [RecipeController::class, 'random'])->name('recipes.random');
Route::get('/search/suggest', [RecipeController::class, 'suggest'])->name('recipes.suggest');
Route::post('/chat', [ChatController::class, 'ask'])->middleware('throttle:12,1')->name('chat.ask');

// ===== Đăng nhập bằng Google (chỉ dành cho khách chưa đăng nhập) =====
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware('auth')->name('dashboard');

// ===== Cần đăng nhập =====
Route::middleware('auth')->group(function () {
    // Hồ sơ cá nhân (của Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Công thức: phải đặt TRƯỚC route /recipes/{recipe}
    Route::get('/my-recipes', [RecipeController::class, 'mine'])->name('recipes.mine');
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Yêu thích và bình luận
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/recipes/{recipe}/favorite', [FavoriteController::class, 'toggle'])->name('recipes.favorite');
    Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

// ===== Khu vực Admin =====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);

    Route::get('recipes', [\App\Http\Controllers\Admin\RecipeController::class, 'index'])->name('recipes.index');
    Route::get('recipes/trash', [\App\Http\Controllers\Admin\RecipeController::class, 'trash'])->name('recipes.trash');
    Route::patch('recipes/{recipe}/approve', [\App\Http\Controllers\Admin\RecipeController::class, 'approve'])->name('recipes.approve');
    Route::patch('recipes/{recipe}/reject', [\App\Http\Controllers\Admin\RecipeController::class, 'reject'])->name('recipes.reject');
    Route::patch('recipes/{recipe}/feature', [\App\Http\Controllers\Admin\RecipeController::class, 'toggleFeatured'])->name('recipes.feature');
    Route::delete('recipes/{recipe}', [\App\Http\Controllers\Admin\RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::patch('recipes/{id}/restore', [\App\Http\Controllers\Admin\RecipeController::class, 'restore'])->name('recipes.restore');
    Route::delete('recipes/{id}/force', [\App\Http\Controllers\Admin\RecipeController::class, 'forceDelete'])->name('recipes.force');
});

require __DIR__ . '/auth.php';
