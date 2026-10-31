<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'image_path',
        'cook_time',
        'servings',
        'difficulty',
        'status',
        'is_featured',
        'rejection_reason',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_number');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function favoritedBy()
    {
        // Tham số: Model liên kết, tên bảng, khóa ngoại của Model hiện tại (recipe_id), khóa ngoại của Model liên kết (user_id)
        return $this->belongsToMany(User::class, 'favorites', 'recipe_id', 'user_id')->withTimestamps();
    }

    // Dùng trong view: $recipe->image_url
    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : 'https://placehold.co/800x600/FFE8D6/FF6B1A?text=Cook+cung+1045';
    }

    // Dùng trong view: $recipe->difficulty_label
    public function getDifficultyLabelAttribute()
    {
        return ['easy' => 'Dễ', 'medium' => 'Trung bình', 'hard' => 'Khó'][$this->difficulty] ?? $this->difficulty;
    }
}
