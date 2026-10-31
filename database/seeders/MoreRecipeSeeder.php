<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoreRecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Chia đều tác giả cho các thành viên (nếu chưa có thì lấy người đầu tiên)
        $authors = User::where('role', 'user')->get();
        if ($authors->isEmpty()) {
            $authors = User::all();
        }
        if ($authors->isEmpty()) {
            return;
        }

        foreach ($this->recipes() as $i => $item) {
            // Bỏ qua nếu món đã tồn tại (chạy lại nhiều lần không bị trùng)
            if (Recipe::withTrashed()->where('title', $item['title'])->exists()) {
                continue;
            }

            // Thiếu danh mục thì dùng danh mục đầu tiên để vẫn tạo được món
            $category = Category::where('name', $item['category'])->first() ?? Category::first();
            if (!$category) {
                return;
            }

            $recipe = Recipe::create([
                'user_id'     => $authors[$i % $authors->count()]->id,
                'category_id' => $category->id,
                'title'       => $item['title'],
                'description' => $item['description'],
                'cook_time'   => $item['cook_time'],
                'servings'    => $item['servings'],
                'difficulty'  => $item['difficulty'],
                'status'      => 'approved',
            ]);

            foreach ($item['ingredients'] as [$name, $qty]) {
                $recipe->ingredients()->create(['name' => $name, 'quantity' => $qty]);
            }
            foreach ($item['steps'] as $n => $content) {
                $recipe->steps()->create(['step_number' => $n + 1, 'content' => $content]);
            }
        }
    }

    private function recipes(): array
    {
        return [
            [
                'category' => 'Món chính',
                'title' => 'Gà kho gừng',
                'description' => 'Gà kho đậm đà, thơm nồng mùi gừng, nước kho sánh quyện, ăn cùng cơm trắng nóng rất đưa cơm.',
                'cook_time' => 45,
                'servings' => 4,
                'difficulty' => 'medium',
                'ingredients' => [
                    ['Đùi gà', '600g'],
                    ['Gừng', '50g'],
                    ['Hành tím', '2 củ'],
                    ['Nước mắm', '2 muỗng canh'],
                    ['Đường', '1 muỗng canh'],
                    ['Nước dừa tươi', '200ml'],
                    ['Tiêu xay', '1/2 muỗng cà phê'],
                ],
                'steps' => [
                    'Gà chặt miếng vừa ăn, rửa sạch với muối và gừng để khử mùi.',
                    'Ướp gà với nước mắm, đường, hành tím băm và tiêu trong 20 phút.',
                    'Phi thơm gừng thái sợi, cho gà vào xào cho săn lại.',
                    'Đổ nước dừa vào, đun lửa nhỏ khoảng 25 phút đến khi nước kho sánh.',
                    'Nêm lại cho vừa ăn, rắc thêm tiêu và dùng nóng.',
                ],
            ],
            [
                'category' => 'Món chính',
                'title' => 'Cơm chiên dương châu',
                'description' => 'Hạt cơm tơi vàng, đủ màu sắc của trứng, lạp xưởng, tôm và rau củ, làm nhanh trong 20 phút.',
                'cook_time' => 20,
                'servings' => 2,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['Cơm nguội', '2 chén'],
                    ['Trứng gà', '2 quả'],
                    ['Lạp xưởng', '2 cây'],
                    ['Tôm sú', '100g'],
                    ['Đậu Hà Lan, cà rốt hạt lựu', '100g'],
                    ['Hành lá', '2 cây'],
                    ['Nước tương', '1 muỗng canh'],
                ],
                'steps' => [
                    'Lạp xưởng thái hạt lựu, tôm bóc vỏ, cà rốt và đậu cho vào bát.',
                    'Đập trứng vào chảo nóng, đảo cho tơi rồi múc ra đĩa.',
                    'Xào lạp xưởng, tôm, cà rốt và đậu cho chín.',
                    'Cho cơm nguội vào, đảo đều tay trên lửa lớn cho hạt cơm tơi.',
                    'Nêm nước tương, trộn trứng và hành lá rồi tắt bếp.',
                ],
            ],
            [
                'category' => 'Món chay',
                'title' => 'Nấm rơm xào sả ớt',
                'description' => 'Nấm rơm giòn ngọt, thơm mùi sả, cay nhẹ vị ớt, món chay đơn giản mà đậm đà.',
                'cook_time' => 15,
                'servings' => 2,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['Nấm rơm', '300g'],
                    ['Sả', '3 cây'],
                    ['Ớt', '1 trái'],
                    ['Tỏi', '3 tép'],
                    ['Nước tương chay', '1 muỗng canh'],
                    ['Dầu ăn', '2 muỗng canh'],
                ],
                'steps' => [
                    'Nấm rơm rửa sạch, cắt đôi và chần sơ qua nước sôi.',
                    'Phi thơm tỏi, sả băm và ớt với dầu ăn.',
                    'Cho nấm vào xào trên lửa lớn khoảng 5 phút.',
                    'Nêm nước tương chay, chút đường và tiêu rồi tắt bếp.',
                ],
            ],
            [
                'category' => 'Tráng miệng',
                'title' => 'Chè khúc bạch',
                'description' => 'Khúc bạch mềm mịn, béo thơm vị sữa, ăn lạnh cùng trái cây và hạnh nhân giòn.',
                'cook_time' => 90,
                'servings' => 6,
                'difficulty' => 'hard',
                'ingredients' => [
                    ['Kem tươi', '200ml'],
                    ['Sữa tươi', '200ml'],
                    ['Gelatin bột', '15g'],
                    ['Đường', '60g'],
                    ['Vải hoặc nhãn đóng hộp', '1 hộp'],
                    ['Hạnh nhân lát', '30g'],
                ],
                'steps' => [
                    'Ngâm gelatin trong nước lạnh khoảng 5 phút cho nở.',
                    'Đun kem, sữa và đường đến khi tan (không để sôi), cho gelatin vào khuấy tan.',
                    'Lọc hỗn hợp qua rây, đổ vào khuôn và để nguội.',
                    'Cho vào ngăn mát tủ lạnh ít nhất 4 giờ cho đông, rồi cắt khúc vuông.',
                    'Xếp khúc bạch ra ly cùng trái cây, rưới siro đường và rắc hạnh nhân.',
                ],
            ],
            [
                'category' => 'Đồ uống',
                'title' => 'Trà đào cam sả',
                'description' => 'Ly trà thanh mát, thơm sả, chua nhẹ vị cam, đào giòn ngọt, giải nhiệt ngày nóng.',
                'cook_time' => 20,
                'servings' => 3,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['Trà đen', '15g'],
                    ['Đào ngâm', '3 miếng'],
                    ['Nước ngâm đào', '100ml'],
                    ['Cam vàng', '2 quả'],
                    ['Sả', '2 cây'],
                    ['Đường', '3 muỗng canh'],
                    ['Đá viên', 'vừa đủ'],
                ],
                'steps' => [
                    'Ủ trà với 500ml nước sôi trong 5 phút, lọc lấy nước và để nguội.',
                    'Đập dập sả, đun cùng chút đường và nước trong 5 phút để lấy hương.',
                    'Vắt cam lấy nước cốt.',
                    'Trộn nước trà, nước sả, nước cam và nước ngâm đào vào bình, nêm cho vừa uống.',
                    'Cho đào thái miếng và đá vào ly, rót trà vào và thưởng thức.',
                ],
            ],
        ];
    }
}
