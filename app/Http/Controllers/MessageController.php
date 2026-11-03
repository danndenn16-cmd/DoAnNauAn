<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Trang trò chuyện của thành viên
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.messages.index');
        }

        $messages = Message::where('user_id', $user->id)->orderBy('id')->get();
        $this->markRead($user->id);

        return view('messages.index', compact('messages'));
    }

    // AJAX: lấy tin mới hơn $after
    public function poll(Request $request)
    {
        $user = auth()->user();
        $after = (int) $request->query('after', 0);

        $new = Message::where('user_id', $user->id)->where('id', '>', $after)->orderBy('id')->get();
        $this->markRead($user->id);

        return response()->json(['messages' => $new->map(fn($m) => $m->toChat($user))->values()]);
    }

    // AJAX: gửi tin cho admin
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_if($user->isAdmin(), 403);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:1000']],
            ['body.required' => 'Bạn chưa nhập nội dung tin nhắn.', 'body.max' => 'Tin nhắn tối đa 1000 ký tự.']
        );

        $message = Message::create([
            'user_id'   => $user->id,
            'sender_id' => $user->id,
            'body'      => $data['body'],
        ]);

        return response()->json(['message' => $message->toChat($user)], 201);
    }

    // AJAX: số tin chưa đọc (cho huy hiệu trên menu), dùng cho cả admin
    public function unread()
    {
        return response()->json(['count' => auth()->user()->unreadMessagesCount()]);
    }

    // Đánh dấu tin của admin gửi cho thành viên là đã đọc
    private function markRead(int $userId): void
    {
        Message::where('user_id', $userId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
