<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('recipes');

        if ($q = $request->q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã thêm danh mục.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category)
    {
        // Không cho xóa danh mục còn công thức (kể cả công thức trong thùng rác),
        // vì khóa ngoại cascade sẽ xóa luôn các công thức đó
        if ($category->recipes()->withTrashed()->exists()) {
            return back()->withErrors([
                'delete' => 'Không thể xóa danh mục "' . $category->name . '" vì vẫn còn công thức thuộc danh mục này.',
            ]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục.');
    }
}
