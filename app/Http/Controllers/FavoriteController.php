<?php

namespace App\Http\Controllers;

use App\Models\Recipe;

class FavoriteController extends Controller
{
    // Danh sách công thức yêu thích của tôi
    public function index()
    {
        $recipes = auth()->user()->favorites()
            ->with(['category', 'user'])
            ->where('status', 'approved')
            ->latest('recipes.created_at')
            ->paginate(9);

        return view('recipes.favorites', compact('recipes'));
    }

    // Bật/tắt yêu thích (trả về JSON cho AJAX)
    public function toggle(Recipe $recipe)
    {
        // toggle(): chưa yêu thích thì thêm, đã yêu thích thì bỏ
        $result = auth()->user()->favorites()->toggle($recipe->id);

        return response()->json([
            'favorited' => count($result['attached']) > 0,
            'count'     => $recipe->favoritedBy()->count(),
        ]);
    }
}