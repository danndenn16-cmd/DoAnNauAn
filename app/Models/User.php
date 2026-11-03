<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Một người dùng có nhiều công thức
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    // Các công thức người dùng đã yêu thích (quan hệ nhiều-nhiều qua bảng favorites)
    public function favorites()
    {
        // Tham số: Model liên kết, tên bảng, khóa ngoại của Model hiện tại (user_id), khóa ngoại của Model liên kết (recipe_id)
        return $this->belongsToMany(Recipe::class, 'favorites', 'user_id', 'recipe_id')->withTimestamps();
    }

    // Các tin nhắn thuộc cuộc trò chuyện của thành viên này
    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    // Số tin chưa đọc: admin đếm tin do thành viên gửi, thành viên đếm tin do admin gửi
    public function unreadMessagesCount(): int
    {
        return $this->isAdmin()
            ? Message::whereColumn('sender_id', 'user_id')->whereNull('read_at')->count()
            : Message::where('user_id', $this->id)->where('sender_id', '!=', $this->id)->whereNull('read_at')->count();
    }

    // Kiểm tra có phải admin không
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Các vai trò trong hệ thống
    public const ROLES = [
        'user'      => 'Thành viên',
        'moderator' => 'Biên tập viên',
        'admin'     => 'Quản trị viên',
    ];

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    // Nhân sự quản trị: Admin hoặc Biên tập viên
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'moderator'], true);
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
