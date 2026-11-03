<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id', 'sender_id', 'body', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    // Chủ cuộc trò chuyện (thành viên)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Người gửi tin này
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Tin này là của "mình" với người đang xem?
    public function isMineFor(User $viewer): bool
    {
        return $viewer->isAdmin()
            ? (int) $this->sender_id !== (int) $this->user_id
            : (int) $this->sender_id === (int) $viewer->id;
    }

    // Dữ liệu trả về cho JavaScript
    public function toChat(User $viewer): array
    {
        return [
            'id'   => $this->id,
            'body' => $this->body,
            'mine' => $this->isMineFor($viewer),
            'time' => $this->created_at->format('H:i d/m'),
        ];
    }
}
