<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Món chính', 'Món canh', 'Món chay', 'Tráng miệng', 'Đồ uống'];

        foreach ($items as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'Các công thức thuộc nhóm ' . mb_strtolower($name),
            ]);
        }
    }
}