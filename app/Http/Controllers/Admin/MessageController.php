<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Danh sách cuộc trò chuyện + nội dung cuộc đang chọn
    public function index(Request $request)
    {
        $threads = User::whereHas('messages')
            ->withCount(['messages as unread_count' => fn($q) => $q
                ->whereColumn('messages.sender_id', 'messages.user_id')
                ->whereNull('messages.read_at')])
            ->withMax('messages', 'created_at')
            ->orderByDesc('messages_max_created_at')
            ->get();

        // Nội dung tin cuối của mỗi cuộc trò chuyện
        $lastBodies = Message::whereIn('id', Message::selectRaw('MAX(id)')->groupBy('user_id'))
            ->pluck('body', 'user_id');

        $activeId = (int) $request->query('user', $threads->first()?->id ?? 0);
        $active = $activeId
            ? ($threads->firstWhere('id', $activeId) ?? User::where('role', '!=', 'admin')->find($activeId))
            : null;

        $messages = collect();
        if ($active) {
            $messages = Message::where('user_id', $active->id)->orderBy('id')->get();
            $this->markRead($active->id);
        }

        return view('admin.messages.index', compact('threads', 'lastBodies', 'active', 'messages'));
    }

    // AJAX: tin mới của một thành viên
    public function poll(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 404);

        $after = (int) $request->query('after', 0);
        $new = Message::where('user_id', $user->id)->where('id', '>', $after)->orderBy('id')->get();
        $this->markRead($user->id);

        return response()->json(['messages' => $new->map(fn($m) => $m->toChat(auth()->user()))->values()]);
    }

    // AJAX: admin trả lời thành viên
    public function store(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Bạn chưa nhập nội dung tin nhắn.', 'body.max' => 'Tin nhắn tối đa 1000 ký tự.']
        );

        $message = Message::create([
            'user_id'   => $user->id,
            'sender_id' => auth()->id(),
            'body'      => $data['body'],
        ]);

        return response()->json(['message' => $message->toChat(auth()->user())], 201);
    }

    // Đánh dấu tin do thành viên gửi là đã đọc
    private function markRead(int $userId): void
    {
        Message::where('user_id', $userId)
            ->whereColumn('sender_id', 'user_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
