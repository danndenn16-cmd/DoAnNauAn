<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'recipes'    => Recipe::count(),
            'pending'    => Recipe::where('status', 'pending')->count(),
            'categories' => Category::count(),
            'users'      => User::count(),
        ];

        $pendingRecipes = Recipe::with(['category', 'user'])
            ->where('status', 'pending')->latest()->take(5)->get();

        $topCategories = Category::withCount('recipes')
            ->orderByDesc('recipes_count')->take(6)->get();

        $topFavorites = Recipe::with('category')
            ->where('status', 'approved')
            ->withCount('favoritedBy')
            ->orderByDesc('favorited_by_count')
            ->take(5)->get();

        $latestUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'pendingRecipes',
            'topCategories',
            'topFavorites',
            'latestUsers'
        ));
    }
}
