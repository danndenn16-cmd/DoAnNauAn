<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Danh sách thành viên: tìm kiếm, lọc theo vai trò, phân trang
    public function index(Request $request)
    {
        $query = User::withCount('recipes');

        if ($q = trim((string) $request->q)) {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $query->where(fn($s) => $s->where('name', 'like', $like)->orWhere('email', 'like', $like));
        }

        if (in_array($request->role, array_keys(User::ROLES), true)) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('role')->latest()->paginate(10)->withQueryString();

        // Số lượng theo vai trò (cho các chip lọc)
        $counts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('admin.users.index', compact('users', 'counts'));
    }

    // Cấp hoặc thu hồi vai trò
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(
            ['role' => ['required', Rule::in(array_keys(User::ROLES))]],
            ['role.required' => 'Vui lòng chọn vai trò.', 'role.in' => 'Vai trò không hợp lệ.']
        );

        // Không cho tự đổi quyền của mình: luôn còn ít nhất một quản trị viên
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể thay đổi vai trò của chính mình.');
        }

        $old = $user->roleLabel();
        $user->role = $data['role'];
        $user->save();

        return back()->with('success', "Đã đổi vai trò của {$user->name}: {$old} → {$user->roleLabel()}.");
    }
}
