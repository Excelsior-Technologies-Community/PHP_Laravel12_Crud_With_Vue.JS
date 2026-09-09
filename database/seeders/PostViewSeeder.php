<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Database\Seeder;

class PostViewSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all();

        foreach ($posts as $post) {
            $days = rand(1, 30);
            $viewsCount = rand(5, 50);

            for ($i = 0; $i < $viewsCount; $i++) {
                PostView::create([
                    'post_id' => $post->id,
                    'user_id' => null,
                    'ip_address' => '127.0.0.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0',
                    'viewed_at' => now()->subDays($days)->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                ]);
            }
        }

        echo 'Created sample views' . PHP_EOL;
        echo 'Total views: ' . PostView::count() . PHP_EOL;
    }
}
