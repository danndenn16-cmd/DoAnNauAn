<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Dữ liệu dùng chung cho menu và footer
        View::composer('layouts.site', function ($view) {
            $view->with('navCategories', Category::orderBy('name')->get(['id', 'name']));
            $view->with('siteStats', [
                'recipes'    => Recipe::where('status', 'approved')->count(),
                'categories' => Category::count(),
                'members'    => User::count(),
            ]);
        });

        // Số công thức chờ duyệt hiện trên menu Admin
        View::composer(['admin._menu', 'layouts.admin'], function ($view) {
            $view->with('pendingCount', Recipe::where('status', 'pending')->count());
        });
    }
}
