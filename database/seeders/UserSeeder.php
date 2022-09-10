<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->times(10)->create();

        $profiles = Profile::where('description', null)->get();
        
        foreach ($profiles as $profile) {
            $profile->image = 'https://picsum.photos/id/'.rand(1,1000).'/400';
            $profile->description = fake()->paragraph(10);
            $profile->save();
        }
        
    }
}
