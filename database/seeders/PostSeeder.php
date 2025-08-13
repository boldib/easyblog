<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     */
    public function run()
    {
        Post::factory()->times(10000)->create();

        $tags = Tag::all()->random(5);
        $posts = Post::where('id', '>', Post::count()-10)->get();

        foreach ($posts as $post) {
            $post->tags()->sync($tags[rand(1, 4)]);
        }
    }
}
