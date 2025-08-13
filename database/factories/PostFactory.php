<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->sentence(rand(1, 8));
        $slug = Str::of($title)->slug();
        $image = 'https://picsum.photos/id/'.rand(1, 1000).'/400';

        return [
            'user_id' => rand(1, User::count()),
            'title' => $title,
            'content' => fake()->paragraph(40),
            'image' => $image,
            'slug' => $slug,
        ];
    }
}
