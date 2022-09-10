<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_user_can_edit_own_profile()
    {
        $user = User::where('id', '2')->first();

        $response = $this->actingAs($user)
        ->get('/profile/edit/'.$user->profile->slug);

        $response->assertStatus(200);
    }

    public function test_user_cannot_edit_other_profile()
    {
        $user1 = User::where('id', rand(11,User::count()))->first();
        $user2 = User::where('id', 10)->first();

        $this->actingAs($user1);

        $response = $this->get('/profile/edit/'.$user2->profile->slug);

        $response->assertStatus(403);
    }
}
