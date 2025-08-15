<?php

namespace Tests\Unit;

use App\Classes\SlugCheck;
use App\Classes\ValidationRuleFactory;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\ProfileRepository;
use App\Services\AuthorizationService;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

/**
 * Test suite for ProfileRepository class.
 * 
 * Tests all major functions including profile retrieval, updates, and deletion.
 * Covers both successful operations and error conditions.
 */
class ProfileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProfileRepository $repository;
    private User $user;
    private Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new ProfileRepository();
        
        // Create test user and profile
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->profile = Profile::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'test-user-' . uniqid(),
            'description' => 'Test description',
        ]);
    }

    /**
     * Test successful profile retrieval by slug.
     */
    public function test_get_profile_returns_profile_by_slug(): void
    {
        $result = $this->repository->getProfile($this->profile->slug);
        
        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals($this->profile->id, $result->id);
        $this->assertEquals($this->profile->slug, $result->slug);
    }

    /**
     * Test profile retrieval with non-existent slug throws exception.
     */
    public function test_get_profile_throws_exception_for_non_existent_slug(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->getProfile('non-existent-slug');
    }

    /**
     * Test successful profile retrieval by ID.
     */
    public function test_get_profile_by_id_returns_profile(): void
    {
        $result = $this->repository->getProfileById($this->profile->id);
        
        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals($this->profile->id, $result->id);
    }

    /**
     * Test profile retrieval with non-existent ID throws exception.
     */
    public function test_get_profile_by_id_throws_exception_for_non_existent_id(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->getProfileById(99999);
    }

    /**
     * Test getting paginated posts for a profile.
     */
    public function test_get_profile_posts_returns_paginated_posts(): void
    {
        // Create test posts
        Post::factory()->count(15)->create([
            'user_id' => $this->user->id,
        ]);
        
        $result = $this->repository->getProfilePosts($this->profile);
        
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertGreaterThan(0, $result->total());
    }

    /**
     * Test successful profile update.
     */
    public function test_update_profile_successfully(): void
    {
        // Mock dependencies
        $this->mockAuthorizationService();
        $this->mockValidationRuleFactory();
        $this->mockSlugCheck(false, false);
        $this->mockImageService();
        
        $request = $this->createMockRequest([
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
            'description' => 'Updated description',
        ]);
        
        $result = $this->repository->update($request, $this->profile->slug, $this->user);
        
        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals('Updated Name', $result->user->name);
        $this->assertEquals('updated-slug', $result->slug);
        $this->assertEquals('Updated description', $result->description);
    }

    /**
     * Test profile update with forbidden slug throws exception.
     */
    public function test_update_profile_with_forbidden_slug_throws_exception(): void
    {
        $this->mockAuthorizationService();
        $this->mockValidationRuleFactory();
        $this->mockSlugCheck(true, false); // Forbidden slug
        
        $request = $this->createMockRequest([
            'name' => 'Updated Name',
            'slug' => 'admin', // Forbidden slug
        ]);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('This URL is already used');
        
        $this->repository->update($request, $this->profile->slug, $this->user);
    }

    /**
     * Test profile update with used slug throws exception.
     */
    public function test_update_profile_with_used_slug_throws_exception(): void
    {
        $this->mockAuthorizationService();
        $this->mockValidationRuleFactory();
        $this->mockSlugCheck(false, true); // Used slug
        
        $request = $this->createMockRequest([
            'name' => 'Updated Name',
            'slug' => 'existing-slug',
        ]);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('This URL is already used');
        
        $this->repository->update($request, $this->profile->slug, $this->user);
    }

    /**
     * Test successful profile deletion.
     */
    public function test_delete_profile_successfully(): void
    {
        $this->mockAuthorizationService();
        
        // Create related data
        Post::factory()->create(['user_id' => $this->user->id]);
        
        $result = $this->repository->delete($this->profile->id, $this->user);
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
        $this->assertDatabaseMissing('profiles', ['id' => $this->profile->id]);
    }

    /**
     * Test profile deletion with non-existent ID throws exception.
     */
    public function test_delete_profile_with_non_existent_id_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->delete(99999, $this->user);
    }

    /**
     * Mock AuthorizationService to avoid authorization checks.
     */
    private function mockAuthorizationService(): void
    {
        // Skip mocking if class already exists (test isolation issue)
        if (class_exists(AuthorizationService::class)) {
            return;
        }
        $mock = Mockery::mock('alias:' . AuthorizationService::class);
        $mock->shouldReceive('canModifyProfile')->andReturn(true);
    }

    /**
     * Mock ValidationRuleFactory to return test rules.
     */
    private function mockValidationRuleFactory(): void
    {
        $mock = Mockery::mock('alias:' . ValidationRuleFactory::class);
        $mock->shouldReceive('getProfileRules')->andReturn([
            'name' => 'required|string|min:2|max:32',
            'slug' => 'required|string|min:3|max:32',
            'description' => 'nullable|string|max:10000',
        ]);
    }

    /**
     * Mock SlugCheck class.
     */
    private function mockSlugCheck(bool $isForbidden, bool $isUsed): void
    {
        $mock = Mockery::mock('overload:' . SlugCheck::class);
        $mock->shouldReceive('isForbidden')->andReturn($isForbidden);
        $mock->shouldReceive('isUsed')->andReturn($isUsed);
    }

    /**
     * Mock ImageService to avoid file operations.
     */
    private function mockImageService(): void
    {
        // Skip mocking if class already exists (test isolation issue)
        if (class_exists(ImageService::class)) {
            return;
        }
        $mock = Mockery::mock('alias:' . ImageService::class);
        $mock->shouldReceive('setProfileImage')->andReturn('test-image.webp');
    }

    /**
     * Create a mock request with validation data.
     */
    private function createMockRequest(array $data): Request
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn($data);
        $request->shouldReceive('file')->andReturn(null);
        $request->shouldReceive('setUserResolver')->andReturnNull();
        
        // Mock global request() function
        $this->app->instance('request', $request);
        
        return $request;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
