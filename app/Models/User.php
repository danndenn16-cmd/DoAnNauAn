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

    // Kiểm tra có phải admin không
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
