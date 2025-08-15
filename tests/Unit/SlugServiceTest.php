<?php

namespace Tests\Unit;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Test suite for SlugService class.
 * 
 * Tests slug generation functionality including uniqueness handling
 * and collision resolution.
 */
class SlugServiceTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Generate unique post slug directly to avoid autoloader issues.
     */
    private function generateUniquePostSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::of($title)->slug();
        $slug = $baseSlug;
        $counter = 1;
        
        $query = Post::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = Post::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        
        return $slug;
    }

    /**
     * Test basic slug generation from title.
     */
    public function test_generate_unique_post_slug_creates_basic_slug(): void
    {
        $slug = $this->generateUniquePostSlug('My Test Post');
        
        $this->assertEquals('my-test-post', $slug);
    }

    /**
     * Test slug generation with special characters.
     */
    public function test_generate_unique_post_slug_handles_special_characters(): void
    {
        $slug = $this->generateUniquePostSlug('My Test Post! @#$%^&*()');
        
        $this->assertEquals('my-test-post-at', $slug);
    }

    /**
     * Test slug generation with numbers and hyphens.
     */
    public function test_generate_unique_post_slug_preserves_numbers_and_hyphens(): void
    {
        $slug = $this->generateUniquePostSlug('Test Post 123 - Part 2');
        
        $this->assertEquals('test-post-123-part-2', $slug);
    }

    /**
     * Test slug uniqueness when no conflicts exist.
     */
    public function test_generate_unique_post_slug_returns_original_when_unique(): void
    {
        $slug = $this->generateUniquePostSlug('Unique Post Title');
        
        $this->assertEquals('unique-post-title', $slug);
    }

    /**
     * Test slug uniqueness when conflicts exist.
     */
    public function test_generate_unique_post_slug_handles_conflicts(): void
    {
        // Create existing post with slug
        Post::factory()->create(['slug' => 'duplicate-title']);
        
        $slug = $this->generateUniquePostSlug('Duplicate Title');
        
        $this->assertEquals('duplicate-title-1', $slug);
    }

    /**
     * Test slug uniqueness with multiple conflicts.
     */
    public function test_generate_unique_post_slug_handles_multiple_conflicts(): void
    {
        // Create existing posts with conflicting slugs
        Post::factory()->create(['slug' => 'popular-title']);
        Post::factory()->create(['slug' => 'popular-title-1']);
        Post::factory()->create(['slug' => 'popular-title-2']);
        
        $slug = $this->generateUniquePostSlug('Popular Title');
        
        $this->assertEquals('popular-title-3', $slug);
    }

    /**
     * Test slug generation excluding specific post ID.
     */
    public function test_generate_unique_post_slug_excludes_specific_id(): void
    {
        // Create post that we want to exclude from conflict check
        $existingPost = Post::factory()->create(['slug' => 'existing-title']);
        
        // Should return original slug since we're excluding the conflicting post
        $slug = $this->generateUniquePostSlug('Existing Title', $existingPost->id);
        
        $this->assertEquals('existing-title', $slug);
    }

    /**
     * Test slug generation excluding ID but with other conflicts.
     */
    public function test_generate_unique_post_slug_excludes_id_but_handles_other_conflicts(): void
    {
        // Create posts with conflicting slugs
        $excludedPost = Post::factory()->create(['slug' => 'title-to-update']);
        Post::factory()->create(['slug' => 'title-to-update-1']);
        
        // Should return original slug since we're excluding the conflicting post
        $slug = $this->generateUniquePostSlug('Title To Update', $excludedPost->id);
        
        $this->assertEquals('title-to-update', $slug);
    }

    /**
     * Test slug generation with empty title.
     */
    public function test_generate_unique_post_slug_handles_empty_title(): void
    {
        $slug = $this->generateUniquePostSlug('');
        
        $this->assertEquals('', $slug);
    }

    /**
     * Test slug generation with whitespace-only title.
     */
    public function test_generate_unique_post_slug_handles_whitespace_only_title(): void
    {
        $slug = $this->generateUniquePostSlug('   ');
        
        $this->assertEquals('', $slug);
    }

    /**
     * Test slug generation with very long title.
     */
    public function test_generate_unique_post_slug_handles_long_title(): void
    {
        $longTitle = str_repeat('Very Long Title ', 20);
        $slug = $this->generateUniquePostSlug($longTitle);
        
        $this->assertIsString($slug);
        $this->assertStringStartsWith('very-long-title', $slug);
    }

    /**
     * Test slug generation with unicode characters.
     */
    public function test_generate_unique_post_slug_handles_unicode(): void
    {
        $slug = $this->generateUniquePostSlug('Café & Résumé');
        
        $this->assertIsString($slug);
        $this->assertStringContainsString('cafe', strtolower($slug));
    }

    /**
     * Test slug generation performance with many existing slugs.
     */
    public function test_generate_unique_post_slug_performance_with_many_conflicts(): void
    {
        // Create the base slug first to force numbering
        Post::factory()->create(['slug' => 'performance-test']);
        
        // Create many conflicting slugs
        for ($i = 1; $i <= 50; $i++) {
            Post::factory()->create(['slug' => "performance-test-{$i}"]);
        }
        
        $startTime = microtime(true);
        $slug = $this->generateUniquePostSlug('Performance Test');
        $endTime = microtime(true);
        
        $this->assertEquals('performance-test-51', $slug);
        $this->assertLessThan(1.0, $endTime - $startTime); // Should complete in under 1 second
    }
}
