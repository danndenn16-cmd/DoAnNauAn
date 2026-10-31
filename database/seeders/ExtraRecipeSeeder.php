<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExtraRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', 'user')->first() ?? User::first();
        if (!$author) {
            return;
        }

        foreach ($this->recipes() as $item) {
            $category = Category::where('name', $item['category'])->first();

            // Bỏ qua nếu thiếu danh mục hoặc món đã tồn tại
            if (!$category || Recipe::withTrashed()->where('title', $item['title'])->exists()) {
                continue;
            }

            $recipe = Recipe::create([
                'user_id'     => $author->id,
                'category_id' => $category->id,
                'title'       => $item['title'],
                'description' => $item['description'],
                'cook_time'   => $item['cook_time'],
                'servings'    => $item['servings'],
                'difficulty'  => $item['difficulty'],
                'status'      => 'approved',
                'is_featured' => $item['featured'] ?? false,
            ]);

            foreach ($item['ingredients'] as [$name, $qty]) {
                $recipe->ingredients()->create(['name' => $name, 'quantity' => $qty]);
            }
            foreach ($item['steps'] as $i => $content) {
                $recipe->steps()->create(['step_number' => $i + 1, 'content' => $content]);
            }
        }
    }

    private function recipes(): array
    {
        return [
            [
                'category' => 'Món chính',
                'title' => 'Bún chả Hà Nội',
                'description' => 'Chả nướng thơm lừng than hoa, ăn cùng bún tươi, rau sống và bát nước chấm chua ngọt đặc trưng.',
                'cook_time' => 60,
                'servings' => 4,
                'difficulty' => 'medium',
                'featured' => true,
                'ingredients' => [
                    ['Thịt ba chỉ', '400g'],
                    ['Thịt nạc vai xay', '300g'],
                    ['Hành tím băm', '2 củ'],
                    ['Nước mắm', '4 muỗng canh'],
                    ['Đường', '3 muỗng canh'],
                    ['Bún tươi', '500g'],
                    ['Rau sống, rau thơm', '1 rổ nhỏ'],
                    ['Đu đủ xanh', '1/2 trái'],
                ],
                'steps' => [
                    'Ướp thịt ba chỉ và thịt xay với hành tím, 1 muỗng nước mắm, 1 muỗng đường trong 30 phút.',
                    'Thịt xay nặn thành viên tròn dẹt, thịt ba chỉ thái miếng vừa ăn.',
                    'Nướng chả hai mặt trên than hoa hoặc chảo chống dính đến khi vàng thơm.',
                    'Pha nước chấm: hòa nước mắm, đường, nước ấm, thêm giấm, tỏi ớt, đu đủ xanh thái sợi.',
                    'Cho chả nóng vào bát nước chấm, dọn kèm bún và rau sống.',
                ],
            ],
            [
                'category' => 'Món chay',
                'title' => 'Đậu hũ sốt cà chua',
                'description' => 'Món chay đơn giản, đậu hũ chiên vàng thấm sốt cà chua chua ngọt, rất hao cơm.',
                'cook_time' => 25,
                'servings' => 3,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['Đậu hũ trắng', '4 miếng'],
                    ['Cà chua', '3 quả'],
                    ['Hành lá', '2 cây'],
                    ['Nước tương', '1 muỗng canh'],
                    ['Đường', '1 muỗng cà phê'],
                    ['Dầu ăn', '2 muỗng canh'],
                ],
                'steps' => [
                    'Đậu hũ cắt miếng vừa ăn, chiên vàng các mặt rồi vớt ra giấy thấm dầu.',
                    'Cà chua cắt múi cau, phi thơm hành rồi xào cà chua cho mềm ra nước.',
                    'Nêm nước tương, đường, thêm chút nước, đun sôi thành sốt.',
                    'Cho đậu hũ vào rim 5 phút cho thấm, rắc hành lá và tắt bếp.',
                ],
            ],
            [
                'category' => 'Tráng miệng',
                'title' => 'Bánh flan caramel',
                'description' => 'Bánh mềm mịn, béo nhẹ vị trứng sữa, phủ lớp caramel cháy cạnh hấp dẫn.',
                'cook_time' => 60,
                'servings' => 6,
                'difficulty' => 'medium',
                'ingredients' => [
                    ['Trứng gà', '4 quả'],
                    ['Sữa tươi không đường', '400ml'],
                    ['Đường (làm flan)', '50g'],
                    ['Đường (làm caramel)', '80g'],
                    ['Vani', '1/2 muỗng cà phê'],
                ],
                'steps' => [
                    'Làm caramel: đun đường với 3 muỗng nước đến khi vàng nâu, rót đáy khuôn.',
                    'Đánh nhẹ trứng với đường, vani rồi đổ sữa ấm vào khuấy đều.',
                    'Lọc hỗn hợp qua rây cho mịn, rót vào khuôn.',
                    'Hấp cách thủy lửa nhỏ 15 đến 20 phút, có phủ giấy bạc để không đọng nước.',
                    'Để nguội, cho ngăn mát tủ lạnh 3 đến 4 giờ rồi úp ra đĩa.',
                ],
            ],
            [
                'category' => 'Đồ uống',
                'title' => 'Sinh tố bơ',
                'description' => 'Ly sinh tố bơ sánh mịn, béo thơm, ngọt dịu, làm trong 10 phút.',
                'cook_time' => 10,
                'servings' => 2,
                'difficulty' => 'easy',
                'ingredients' => [
                    ['Bơ sáp chín', '1 quả'],
                    ['Sữa đặc', '2 muỗng canh'],
                    ['Sữa tươi', '150ml'],
                    ['Đá viên', '1 cốc nhỏ'],
                ],
                'steps' => [
                    'Bơ bổ đôi, bỏ hạt, lấy phần thịt.',
                    'Cho bơ, sữa đặc, sữa tươi và đá vào máy xay.',
                    'Xay nhuyễn khoảng 1 phút, rót ra ly và thưởng thức ngay.',
                ],
            ],
            [
                'category' => 'Món canh',
                'title' => 'Canh khổ qua nhồi thịt',
                'description' => 'Món canh thanh mát ngày hè, khổ qua mềm, nhân thịt đậm vị, nước canh ngọt thanh.',
                'cook_time' => 50,
                'servings' => 4,
                'difficulty' => 'medium',
                'ingredients' => [
                    ['Khổ qua', '2 trái'],
                    ['Thịt heo xay', '250g'],
                    ['Mộc nhĩ', '5g'],
                    ['Miến', '30g'],
                    ['Hành tím', '1 củ'],
                    ['Nước mắm', '1 muỗng canh'],
                ],
                'steps' => [
                    'Khổ qua cắt khúc, bỏ ruột; chần sơ trong nước muối để bớt đắng.',
                    'Trộn thịt xay với mộc nhĩ, miến cắt nhỏ, hành tím, nước mắm và tiêu.',
                    'Nhồi hỗn hợp thịt vào từng khúc khổ qua.',
                    'Đun sôi nước, thả khổ qua vào nấu lửa nhỏ 25 đến 30 phút.',
                    'Nêm vừa ăn, rắc hành ngò và dùng nóng.',
                ],
            ],
        ];
    }
}
