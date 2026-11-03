<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        $admin = User::where('role', 'admin')->first();

        if (!$user || !$admin || Message::where('user_id', $user->id)->exists()) {
            return;
        }

        // [người gửi, nội dung, số phút trước, đã đọc chưa]
        $items = [
            [$user,  'Chào admin, mình muốn hỏi cách đăng công thức kèm ảnh ạ?', 30, true],
            [$admin, 'Chào bạn! Bấm "+ Đăng công thức", chọn ảnh JPG hoặc PNG dưới 2MB rồi gửi, bài sẽ được duyệt sớm nhé.', 25, true],
            [$user,  'Cảm ơn admin, mình sẽ thử ngay!', 20, false],
        ];

        foreach ($items as [$sender, $body, $minutesAgo, $read]) {
            $message = new Message([
                'user_id'   => $user->id,
                'sender_id' => $sender->id,
                'body'      => $body,
                'read_at'   => $read ? now()->subMinutes($minutesAgo - 1) : null,
            ]);
            $message->created_at = now()->subMinutes($minutesAgo);
            $message->updated_at = $message->created_at;
            $message->save();
        }
    }
}
