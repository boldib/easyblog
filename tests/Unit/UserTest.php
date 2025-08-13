<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that a user can edit their own profile.
     */
    public function test_user_can_edit_own_profile(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id, 'slug' => 'test-user']);
        $user->setRelation('profile', $profile);

        $response = $this->actingAs($user)
            ->get('/profile/edit/' . $profile->slug);

        $response->assertStatus(200);
    }

    /**
     * Test that a user cannot edit another user's profile.
     */
    public function test_user_cannot_edit_other_profile(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $profile1 = Profile::factory()->create(['user_id' => $user1->id, 'slug' => 'user1']);
        $profile2 = Profile::factory()->create(['user_id' => $user2->id, 'slug' => 'user2']);
        
        $user1->setRelation('profile', $profile1);
        $user2->setRelation('profile', $profile2);

        $response = $this->actingAs($user1)
            ->get('/profile/edit/' . $profile2->slug);

        $response->assertStatus(403);
    }
}
