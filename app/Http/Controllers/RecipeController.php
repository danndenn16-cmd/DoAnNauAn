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
{ // Trang chủ
    // Trang chủ
    public function index(Request $request)
    {
        $query = Recipe::with(['category', 'user'])->where('status', 'approved');

        if ($q = $request->q) {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('title', 'like', $like)
                    ->orWhereHas('ingredients', fn($i) => $i->where('name', 'like', $like));
            });
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $recipes = $query->latest()->paginate(9)->withQueryString();

        // Danh mục kèm số công thức đã duyệt
        $categories = Category::withCount([
            'recipes' => fn($sub) => $sub->where('status', 'approved'),
        ])->orderBy('name')->get();

        // Món nổi bật và "Được yêu thích": chỉ hiện khi chưa tìm kiếm/lọc và đang ở trang 1
        $showIntro = !$request->q && !$request->category && $recipes->currentPage() === 1;

        $featured = null;
        $popular = collect();

        if ($showIntro) {
            // Ưu tiên món Admin chọn nổi bật (mới chọn gần nhất); chưa có thì lấy món mới nhất
            $featured = Recipe::with(['category', 'user'])
                ->where('status', 'approved')
                ->where('is_featured', true)
                ->orderByDesc('updated_at')
                ->first()
                ?? Recipe::with(['category', 'user'])
                ->where('status', 'approved')
                ->latest()
                ->first();

            $popular = Recipe::with('category')
                ->where('status', 'approved')
                ->withCount('favoritedBy')
                ->orderByDesc('favorited_by_count')
                ->latest()
                ->take(3)
                ->get();
        }

        return view('recipes.index', compact('recipes', 'categories', 'featured', 'popular'));
    }

    // Chi tiết một công thức
    public function show(Recipe $recipe)
    {
        // Công thức chưa duyệt: chỉ chủ bài và admin được xem
        $canSee = auth()->check()
            && (auth()->user()->isStaff() || auth()->id() === $recipe->user_id);

        if ($recipe->status !== 'approved' && !$canSee) {
            abort(404);
        }

        $recipe->load(['category', 'user', 'ingredients', 'steps']);
        $comments = $recipe->comments()->with('user')->latest()->get();

        $isFavorited = auth()->check()
            && $recipe->favoritedBy()->where('users.id', auth()->id())->exists();
        $favCount = $recipe->favoritedBy()->count();

        // Gợi ý: cùng danh mục
        $related = Recipe::with(['category', 'user'])
            ->where('status', 'approved')
            ->where('category_id', $recipe->category_id)
            ->where('id', '!=', $recipe->id)
            ->latest()
            ->take(3)
            ->get();

        return view('recipes.show', compact('recipe', 'comments', 'isFavorited', 'favCount', 'related'));
    }
    // Gợi ý tìm kiếm tức thì (trả về JSON cho AJAX)
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $like = '%' . addcslashes($q, '%_\\') . '%';

        $recipes = Recipe::with('category')
            ->where('status', 'approved')
            ->where(function ($sub) use ($like) {
                $sub->where('title', 'like', $like)
                    ->orWhereHas('ingredients', fn($i) => $i->where('name', 'like', $like));
            })
            ->latest()
            ->take(6)
            ->get();

        return response()->json([
            'data' => $recipes->map(fn($r) => [
                'title'     => $r->title,
                'category'  => $r->category->name,
                'cook_time' => $r->cook_time,
                'image'     => $r->image_url,
                'url'       => route('recipes.show', $r),
            ]),
        ]);
    }
    // Gợi ý một món ngẫu nhiên
    public function random()
    {
        $recipe = Recipe::where('status', 'approved')->inRandomOrder()->first();

        if (!$recipe) {
            return redirect()->route('home')->with('success', 'Chưa có công thức nào để gợi ý.');
        }

        return redirect()->route('recipes.show', $recipe);
    }
    // Công thức của tôi (lọc theo trạng thái)
    public function mine(Request $request)
    {
        // Đếm theo trạng thái bằng một câu truy vấn
        $byStatus = Recipe::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'all'      => $byStatus->sum(),
            'pending'  => $byStatus['pending'] ?? 0,
            'approved' => $byStatus['approved'] ?? 0,
            'rejected' => $byStatus['rejected'] ?? 0,
        ];

        $query = Recipe::with('category')->where('user_id', auth()->id());

        if (in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $recipes = $query->latest()->paginate(8)->withQueryString();

        return view('recipes.mine', compact('recipes', 'counts'));
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
                'category_id'      => $data['category_id'],
                'title'            => $data['title'],
                'description'      => $data['description'] ?? null,
                'image_path'       => $imagePath,
                'cook_time'        => $data['cook_time'],
                'servings'         => $data['servings'],
                'difficulty'       => $data['difficulty'],
                // User sửa thì phải duyệt lại, Admin sửa thì giữ nguyên trạng thái
                'status'           => auth()->user()->isAdmin() ? $recipe->status : 'pending',
                'rejection_reason' => auth()->user()->isAdmin() ? $recipe->rejection_reason : null,
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

        if (auth()->user()->isStaff() && $recipe->user_id !== auth()->id()) {
            return redirect()->route('admin.recipes.index')->with('success', 'Đã chuyển công thức vào thùng rác.');
        }

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
