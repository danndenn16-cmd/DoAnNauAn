<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeRequest;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    // Trang chủ: danh sách công thức đã duyệt
    public function index(Request $request)
    {
        $query = Recipe::with(['category', 'user'])->where('status', 'approved');

        if ($q = $request->q) {
            $query->where('title', 'like', "%{$q}%");
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $recipes = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::withCount('recipes')->get();

        return view('recipes.index', compact('recipes', 'categories'));
    }

    // Chi tiết một công thức
    public function show(Recipe $recipe)
    {
        // Công thức chưa duyệt: chỉ chủ bài và admin được xem
        $canSee = auth()->check()
            && (auth()->user()->isAdmin() || auth()->id() === $recipe->user_id);

        if ($recipe->status !== 'approved' && !$canSee) {
            abort(404);
        }

        $recipe->load(['category', 'user', 'ingredients', 'steps']);
        $comments = $recipe->comments()->with('user')->latest()->get();

        $isFavorited = auth()->check()
            && $recipe->favoritedBy()->where('favorites.user_id', auth()->id())->exists();

        $favCount = $recipe->favoritedBy()->count();

        return view('recipes.show', compact('recipe', 'comments', 'isFavorited', 'favCount'));
    }

    // Công thức của tôi
    public function mine()
    {
        $recipes = Recipe::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('recipes.mine', compact('recipes'));
    }

    // Form thêm mới
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('recipes.create', compact('categories'));
    }

    // Lưu công thức mới
    public function store(RecipeRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $recipe = Recipe::create([
                'user_id'     => auth()->id(),
                'category_id' => $data['category_id'],
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'image_path'  => $request->hasFile('image')
                    ? $request->file('image')->store('recipes', 'public')
                    : null,
                'cook_time'   => $data['cook_time'],
                'servings'    => $data['servings'],
                'difficulty'  => $data['difficulty'],
                // Admin đăng thì duyệt luôn, user thường phải chờ duyệt
                'status'      => auth()->user()->isAdmin() ? 'approved' : 'pending',
            ]);

            $this->saveChildren($recipe, $data);
        });

        $msg = auth()->user()->isAdmin()
            ? 'Đăng công thức thành công.'
            : 'Đã gửi công thức, vui lòng chờ quản trị viên duyệt.';

        return redirect()->route('recipes.mine')->with('success', $msg);
    }

    // Form sửa
    public function edit(Recipe $recipe)
    {
        Gate::authorize('update', $recipe);

        $recipe->load(['ingredients', 'steps']);
        $categories = Category::orderBy('name')->get();

        return view('recipes.edit', compact('recipe', 'categories'));
    }

    // Cập nhật
    public function update(RecipeRequest $request, Recipe $recipe)
    {
        Gate::authorize('update', $recipe);

        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $recipe) {
            $imagePath = $recipe->image_path;

            if ($request->hasFile('image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('recipes', 'public');
            }

            $recipe->update([
                'category_id' => $data['category_id'],
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'image_path'  => $imagePath,
                'cook_time'   => $data['cook_time'],
                'servings'    => $data['servings'],
                'difficulty'  => $data['difficulty'],
                'status'      => auth()->user()->isAdmin() ? $recipe->status : 'pending',
            ]);

            // Xóa nguyên liệu và bước cũ, lưu lại bản mới
            $recipe->ingredients()->delete();
            $recipe->steps()->delete();
            $this->saveChildren($recipe, $data);
        });

        return redirect()->route('recipes.mine')->with('success', 'Cập nhật công thức thành công.');
    }

    // Xóa (xóa mềm)
    public function destroy(Recipe $recipe)
    {
        Gate::authorize('delete', $recipe);

        $recipe->delete();

        return redirect()->route('recipes.mine')->with('success', 'Đã xóa công thức.');
    }

    // Hàm phụ: lưu nguyên liệu và các bước
    private function saveChildren(Recipe $recipe, array $data): void
    {
        foreach ($data['ingredients'] as $ing) {
            $recipe->ingredients()->create([
                'name'     => $ing['name'],
                'quantity' => $ing['quantity'] ?? null,
            ]);
        }

        foreach (array_values($data['steps']) as $i => $content) {
            $recipe->steps()->create([
                'step_number' => $i + 1,
                'content'     => $content,
            ]);
        }
    }
}
