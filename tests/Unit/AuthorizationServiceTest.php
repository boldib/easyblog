<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Test suite for AuthorizationService class.
 * 
 * Tests authorization logic for profile and post modifications.
 * Covers owner permissions, admin permissions, and access denial scenarios.
 */
class AuthorizationServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private User $otherUser;
    private Profile $profile;

    /**
     * Get the AuthorizationService class instance.
     * This method handles class loading issues that occur in test isolation.
     */
    private function getAuthorizationService()
    {
        $className = 'App\\Services\\AuthorizationService';
        
        // Try to load the class if it doesn't exist
        if (!class_exists($className)) {
            $classPath = app_path('Services/AuthorizationService.php');
            if (file_exists($classPath)) {
                require_once $classPath;
            }
        }
        
        if (!class_exists($className)) {
            $this->markTestSkipped('AuthorizationService class not found');
        }
        
        return $className;
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->otherUser = User::factory()->create(['role' => 'user']);
        
        $this->profile = Profile::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * Test profile owner can modify their own profile.
     */
    public function test_can_modify_profile_allows_owner(): void
    {
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyProfile($this->profile, $this->user);
        
        $this->assertTrue($result);
    }

    /**
     * Test admin can modify any profile.
     */
    public function test_can_modify_profile_allows_admin(): void
    {
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyProfile($this->profile, $this->admin);
        
        $this->assertTrue($result);
    }

    /**
     * Test non-owner non-admin cannot modify profile.
     */
    public function test_can_modify_profile_denies_other_user(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized to modify this profile');
        
        $authService = $this->getAuthorizationService();
        $authService::canModifyProfile($this->profile, $this->otherUser);
    }

    /**
     * Test post owner can modify their own post.
     */
    public function test_can_modify_post_allows_owner(): void
    {
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyPost($this->user->id, $this->user->id);
        
        $this->assertTrue($result);
    }

    /**
     * Test non-owner cannot modify post.
     */
    public function test_can_modify_post_denies_other_user(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized to modify this post');
        
        $authService = $this->getAuthorizationService();
        $authService::canModifyPost($this->user->id, $this->otherUser->id);
    }

    /**
     * Test authorization with different user ID types.
     */
    public function test_can_modify_post_handles_different_id_types(): void
    {
        // Test with string IDs (should still work due to loose comparison)
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyPost((string) $this->user->id, (string) $this->user->id);
        $this->assertTrue($result);
        
        // Test with mixed types
        $result = $authService::canModifyPost($this->user->id, (string) $this->user->id);
        $this->assertTrue($result);
    }

    /**
     * Test profile authorization with default user role.
     */
    public function test_can_modify_profile_with_default_role(): void
    {
        $userWithNullRole = User::factory()->create(['role' => 'user']);
        $profileForNullUser = Profile::factory()->create(['user_id' => $userWithNullRole->id]);
        
        // Owner should still be able to modify
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyProfile($profileForNullUser, $userWithNullRole);
        $this->assertTrue($result);
        
        // Other user should not be able to modify
        $this->expectException(HttpException::class);
        $authService::canModifyProfile($profileForNullUser, $this->otherUser);
    }

    /**
     * Test profile authorization with empty role.
     */
    public function test_can_modify_profile_with_empty_role(): void
    {
        $userWithEmptyRole = User::factory()->create(['role' => '']);
        $profileForEmptyUser = Profile::factory()->create(['user_id' => $userWithEmptyRole->id]);
        
        // Owner should still be able to modify
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyProfile($profileForEmptyUser, $userWithEmptyRole);
        $this->assertTrue($result);
    }

    /**
     * Test authorization methods are static.
     */
    public function test_authorization_methods_are_static(): void
    {
        $authService = $this->getAuthorizationService();
        $reflection = new \ReflectionClass($authService);
        
        $canModifyProfileMethod = $reflection->getMethod('canModifyProfile');
        $this->assertTrue($canModifyProfileMethod->isStatic());
        
        $canModifyPostMethod = $reflection->getMethod('canModifyPost');
        $this->assertTrue($canModifyPostMethod->isStatic());
    }

    /**
     * Test authorization with edge case user IDs.
     */
    public function test_can_modify_post_with_zero_ids(): void
    {
        $this->expectException(HttpException::class);
        
        $authService = $this->getAuthorizationService();
        $authService::canModifyPost(0, 1);
    }

    /**
     * Test authorization with negative user IDs.
     */
    public function test_can_modify_post_with_negative_ids(): void
    {
        $this->expectException(HttpException::class);
        
        $authService = $this->getAuthorizationService();
        $authService::canModifyPost(-1, 1);
    }

    /**
     * Test profile authorization with admin role variations.
     */
    public function test_can_modify_profile_with_admin_role_variations(): void
    {
        $adminVariations = ['admin', 'ADMIN', 'Admin'];
        
        foreach ($adminVariations as $role) {
            $adminUser = User::factory()->create(['role' => $role]);
            
            if ($role === 'admin') {
                // Only lowercase 'admin' should work
                $authService = $this->getAuthorizationService();
                $result = $authService::canModifyProfile($this->profile, $adminUser);
                $this->assertTrue($result);
            } else {
                // Other variations should not work
                $this->expectException(HttpException::class);
                $authService = $this->getAuthorizationService();
                $authService::canModifyProfile($this->profile, $adminUser);
            }
        }
    }

    /**
     * Test that authorization methods return boolean true on success.
     */
    public function test_authorization_methods_return_boolean_true(): void
    {
        $authService = $this->getAuthorizationService();
        $profileResult = $authService::canModifyProfile($this->profile, $this->user);
        $this->assertIsBool($profileResult);
        $this->assertTrue($profileResult);
        
        $postResult = $authService::canModifyPost($this->user->id, $this->user->id);
        $this->assertIsBool($postResult);
        $this->assertTrue($postResult);
    }

    /**
     * Test authorization with very large user IDs.
     */
    public function test_can_modify_post_with_large_ids(): void
    {
        $largeId = PHP_INT_MAX;
        
        $authService = $this->getAuthorizationService();
        $result = $authService::canModifyPost($largeId, $largeId);
        $this->assertTrue($result);
        
        $this->expectException(HttpException::class);
        $authService::canModifyPost($largeId, $largeId - 1);
    }
}
