<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    // Danh sách công thức (lọc theo trạng thái, tìm theo tên)
    public function index(Request $request)
    {
        $query = Recipe::with(['category', 'user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($q = $request->q) {
            $query->where('title', 'like', "%{$q}%");
        }

        $recipes = $query->latest()->paginate(10)->withQueryString();

        return view('admin.recipes.index', compact('recipes'));
    }

    // Duyệt (hoặc duyệt lại bài bị từ chối)
    public function approve(Recipe $recipe)
    {
        $recipe->status = 'approved';
        $recipe->rejection_reason = null;
        $recipe->save();

        return back()->with('success', 'Đã duyệt công thức "' . $recipe->title . '".');
    }

    // Từ chối / gỡ bài, bắt buộc có lý do
    public function reject(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Vui lòng nhập lý do từ chối.',
            'reason.max'      => 'Lý do tối đa 500 ký tự.',
        ]);

        $recipe->status = 'rejected';
        $recipe->rejection_reason = $data['reason'];
        $recipe->is_featured = false;
        $recipe->save();

        return back()->with('success', 'Đã từ chối công thức "' . $recipe->title . '".');
    }

    // Bật/tắt nổi bật (chỉ bài đã duyệt)
    public function toggleFeatured(Recipe $recipe)
    {
        if ($recipe->status !== 'approved') {
            return back()->withErrors(['feature' => 'Chỉ công thức đã duyệt mới được chọn nổi bật.']);
        }

        $recipe->is_featured = !$recipe->is_featured;
        $recipe->save();

        return back()->with('success', $recipe->is_featured
            ? 'Đã đặt làm công thức nổi bật.'
            : 'Đã bỏ nổi bật.');
    }

    // Xóa mềm (đưa vào thùng rác)
    public function destroy(Recipe $recipe)
    {
        $recipe->is_featured = false;
        $recipe->save();
        $recipe->delete();

        return back()->with('success', 'Đã chuyển công thức vào thùng rác.');
    }

    // Thùng rác
    public function trash()
    {
        $recipes = Recipe::onlyTrashed()->with(['category', 'user'])
            ->latest('deleted_at')->paginate(10);

        return view('admin.recipes.trash', compact('recipes'));
    }

    public function restore($id)
    {
        Recipe::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Đã khôi phục công thức.');
    }

    // Xóa vĩnh viễn (xóa luôn ảnh)
    public function forceDelete($id)
    {
        $recipe = Recipe::onlyTrashed()->findOrFail($id);

        if ($recipe->image_path) {
            Storage::disk('public')->delete($recipe->image_path);
        }
        $recipe->forceDelete();

        return back()->with('success', 'Đã xóa vĩnh viễn công thức.');
    }
}
