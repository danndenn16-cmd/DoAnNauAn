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
            : 'https://placehold.co/600x400?text=Recipe';
    }
}
