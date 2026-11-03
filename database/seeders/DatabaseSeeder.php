<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Nguyễn Văn A',
            'email'    => 'user@example.com',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Biên tập viên',
            'email'    => 'moderator@example.com',
            'password' => Hash::make('mod123'),
            'role'     => 'moderator',
        ]);
        $this->call([
            CategorySeeder::class,
            RecipeSeeder::class,
            ExtraRecipeSeeder::class,
            MoreRecipeSeeder::class,
            MessageSeeder::class
        ]);
    }
}
