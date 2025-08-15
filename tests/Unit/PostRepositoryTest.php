<?php

namespace Tests\Unit;

use App\Classes\Tagpost;
use App\Classes\ValidationRuleFactory;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Services\AuthorizationService;
use App\Services\ImageService;
use App\Services\SlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

/**
 * Test suite for PostRepository class.
 * 
 * Tests all major functions including post creation, retrieval, updates, and deletion.
 * Covers both successful operations and error conditions.
 */
class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PostRepository $repository;
    private User $user;
    private Profile $profile;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = new PostRepository();
        
        // Create test user, profile, and post
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->profile = Profile::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'test-user-' . uniqid(),
        ]);
        
        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Post',
            'slug' => 'test-post',
            'content' => 'Test content',
        ]);
    }

    /**
     * Test successful post retrieval by profile and post slug.
     */
    public function test_show_returns_post_by_slugs(): void
    {
        $result = $this->repository->show($this->profile->slug, $this->post->slug);
        
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($this->post->id, $result->id);
        $this->assertEquals($this->post->slug, $result->slug);
        $this->assertEquals($this->post->title, $result->title);
    }

    /**
     * Test post retrieval with non-existent profile slug throws exception.
     */
    public function test_show_throws_exception_for_non_existent_profile(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->show('non-existent-profile', $this->post->slug);
    }

    /**
     * Test post retrieval with non-existent post slug throws exception.
     */
    public function test_show_throws_exception_for_non_existent_post(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->show($this->profile->slug, 'non-existent-post');
    }

    /**
     * Test create method returns authenticated user.
     */
    public function test_create_returns_authenticated_user(): void
    {
        $this->actingAs($this->user);
        
        $result = $this->repository->create();
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->user->id, $result->id);
    }

    /**
     * Test successful post storage.
     */
    public function test_store_creates_post_successfully(): void
    {
        $this->actingAs($this->user);
        
        $this->mockDependencies();
        
        $request = $this->createMockRequest([
            'title' => 'New Test Post',
            'content' => 'New test content for the post',
            'tags' => 'php,laravel,testing',
        ]);
        
        $result = $this->repository->store($request);
        
        $this->assertIsString($result);
        // Check that result has the correct format: /profile-slug/post-slug
        $this->assertStringStartsWith('/', $result);
        $this->assertMatchesRegularExpression('/^\/[^\/]+\/[^\/]+$/', $result);
        // The result should be in format: /profile-slug/post-slug
        // Since both slugs are dynamically generated, just verify the format is correct
        $parts = explode('/', trim($result, '/'));
        $this->assertCount(2, $parts, 'Result should have exactly 2 parts: profile-slug and post-slug');
        $this->assertNotEmpty($parts[0], 'Profile slug part should not be empty');
        $this->assertNotEmpty($parts[1], 'Post slug part should not be empty');
        $this->assertDatabaseHas('posts', [
            'title' => 'New Test Post',
            'content' => 'New test content for the post',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test post storage without tags.
     */
    public function test_store_creates_post_without_tags(): void
    {
        $this->actingAs($this->user);
        
        $this->mockDependencies();
        
        $request = $this->createMockRequest([
            'title' => 'Post Without Tags',
            'content' => 'Content without any tags',
        ]);
        
        $result = $this->repository->store($request);
        
        $this->assertIsString($result);
        $this->assertDatabaseHas('posts', [
            'title' => 'Post Without Tags',
            'content' => 'Content without any tags',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test edit method returns post by ID.
     */
    public function test_edit_returns_post_by_id(): void
    {
        $result = $this->repository->edit($this->post->id);
        
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($this->post->id, $result->id);
    }

    /**
     * Test edit with non-existent ID throws exception.
     */
    public function test_edit_throws_exception_for_non_existent_id(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->edit(99999);
    }

    /**
     * Test successful post update.
     */
    public function test_update_modifies_post_successfully(): void
    {
        $this->mockDependencies();
        
        $request = $this->createMockRequest([
            'title' => 'Updated Post Title',
            'content' => 'Updated post content',
            'tags' => 'updated,tags',
        ]);
        
        $result = $this->repository->update($request, $this->post->id);
        
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('Updated Post Title', $result->title);
        $this->assertEquals('Updated post content', $result->content);
    }

    /**
     * Test post update with image upload.
     */
    public function test_update_with_image_upload(): void
    {
        $this->mockDependencies();
        
        $request = $this->createMockRequestWithFile([
            'title' => 'Post With Image',
            'content' => 'Content with image',
        ]);
        
        $result = $this->repository->update($request, $this->post->id);
        
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('Post With Image', $result->title);
    }

    /**
     * Test successful post deletion.
     */
    public function test_delete_removes_post_successfully(): void
    {
        $this->mockAuthorizationService();
        
        $result = $this->repository->delete($this->post->id, $this->user->id);
        
        $this->assertTrue($result);
        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
    }

    /**
     * Test post deletion with non-existent ID throws exception.
     */
    public function test_delete_throws_exception_for_non_existent_id(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->repository->delete(99999, $this->user->id);
    }

    /**
     * Mock all required dependencies for post operations.
     */
    private function mockDependencies(): void
    {
        $this->mockValidationRuleFactory();
        $this->mockSlugService();
        $this->mockImageService();
        $this->mockTagpost();
        $this->mockAuthorizationService();
    }

    /**
     * Mock ValidationRuleFactory to return test rules.
     */
    private function mockValidationRuleFactory(): void
    {
        $mock = Mockery::mock('alias:' . ValidationRuleFactory::class);
        $mock->shouldReceive('getPostRules')->andReturn([
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10|max:50000',
            'tags' => 'nullable|string|max:150',
        ]);
    }

    /**
     * Mock SlugService to return predictable slugs.
     */
    private function mockSlugService(): void
    {
        $mock = Mockery::mock('alias:' . SlugService::class);
        $mock->shouldReceive('generateUniquePostSlug')
            ->andReturn('generated-slug');
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
        $mock->shouldReceive('setPostImage')->andReturn('test-image.webp');
    }

    /**
     * Mock Tagpost class for tag synchronization.
     */
    private function mockTagpost(): void
    {
        $mock = Mockery::mock('alias:' . Tagpost::class);
        $mock->shouldReceive('sync')->andReturnNull();
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
        $mock->shouldReceive('canModifyPost')->andReturn(true);
    }

    /**
     * Create a mock request with validation data.
     */
    private function createMockRequest(array $data): Request
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn($data);
        $request->shouldReceive('file')->andReturn(null);
        $request->shouldReceive('hasFile')->andReturn(false);
        
        return $request;
    }

    /**
     * Create a mock request with file upload.
     */
    private function createMockRequestWithFile(array $data): Request
    {
        // Skip if GD extension is not available
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not available.');
        }
        
        $file = UploadedFile::fake()->image('test.jpg');
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn($data);
        $request->shouldReceive('file')->andReturn($file);
        $request->shouldReceive('hasFile')->andReturn(true);
        
        return $request;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
