<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем посты с привязкой к случайным пользователям
        Post::factory()->count(20)->create([
            'user_id' => User::inRandomOrder()->first()->id,
        ]);
    }
}