<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'slug' => fake()->unique()->slug(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'image' => 'default.webp',
            'links' => json_encode([
                'website' => fake()->url(),
                'twitter' => '@' . fake()->userName(),
            ]),
        ];
    }
}
