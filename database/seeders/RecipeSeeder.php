<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'user')->first();

        $data = [
            [
                'category' => 'Món chính',
                'title' => 'Thịt kho trứng',
                'description' => 'Món ăn truyền thống ngày Tết, thịt mềm, nước kho đậm đà.',
                'cook_time' => 90, 'servings' => 4, 'difficulty' => 'medium',
                'ingredients' => [
                    ['Thịt ba chỉ', '500g'], ['Trứng vịt', '6 quả'],
                    ['Nước dừa tươi', '500ml'], ['Nước mắm', '3 muỗng canh'],
                ],
                'steps' => [
                    'Thịt rửa sạch, cắt khối vuông, ướp nước mắm 30 phút.',
                    'Luộc trứng, bóc vỏ.',
                    'Thắng đường tạo màu, cho thịt vào đảo đều.',
                    'Đổ nước dừa, thêm trứng, hầm lửa nhỏ 60 phút.',
                ],
            ],
            [
                'category' => 'Món canh',
                'title' => 'Canh chua cá lóc',
                'description' => 'Vị chua thanh của me, thơm mùi rau om, ngò gai.',
                'cook_time' => 40, 'servings' => 4, 'difficulty' => 'easy',
                'ingredients' => [
                    ['Cá lóc', '1 con'], ['Me chua', '50g'],
                    ['Cà chua', '2 quả'], ['Dứa', '1/4 quả'],
                ],
                'steps' => [
                    'Cá làm sạch, cắt khúc.',
                    'Nấu nước me, cho dứa và cà chua vào.',
                    'Thả cá vào nấu chín, nêm vừa ăn, thêm rau om.',
                ],
            ],
            [
                'category' => 'Tráng miệng',
                'title' => 'Chè đậu xanh',
                'description' => 'Món chè mát lạnh, ngọt dịu cho ngày hè.',
                'cook_time' => 45, 'servings' => 3, 'difficulty' => 'easy',
                'ingredients' => [
                    ['Đậu xanh', '200g'], ['Đường', '100g'], ['Nước cốt dừa', '100ml'],
                ],
                'steps' => [
                    'Ngâm đậu xanh 2 giờ.',
                    'Nấu đậu mềm, thêm đường.',
                    'Múc ra ly, rưới nước cốt dừa.',
                ],
            ],
        ];

        foreach ($data as $item) {
            $recipe = Recipe::create([
                'user_id' => $user->id,
                'category_id' => Category::where('name', $item['category'])->first()->id,
                'title' => $item['title'],
                'description' => $item['description'],
                'cook_time' => $item['cook_time'],
                'servings' => $item['servings'],
                'difficulty' => $item['difficulty'],
                'status' => 'approved',
            ]);

            foreach ($item['ingredients'] as [$name, $qty]) {
                $recipe->ingredients()->create(['name' => $name, 'quantity' => $qty]);
            }

            foreach ($item['steps'] as $i => $content) {
                $recipe->steps()->create(['step_number' => $i + 1, 'content' => $content]);
            }
        }
    }
}