<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     */
    public function run()
    {
        User::factory()->times(10)->create();

        $profiles = Profile::where('description', null)->get();

        foreach ($profiles as $profile) {
            $profile->image = 'https://picsum.photos/id/'.rand(1, 1000).'/400';
            $profile->description = fake()->paragraph(10);
            $profile->save();
        }
    }
}
