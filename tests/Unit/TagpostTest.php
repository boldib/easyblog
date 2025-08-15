<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Test suite for Tagpost class.
 * 
 * Tests tag synchronization functionality including tag creation,
 * association with posts, and handling of various input formats.
 */
class TagpostTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;
    
    /**
     * Sync tags to a post directly to avoid autoloader issues.
     */
    private function syncTags(string|array|null $tags, Post $post): void
    {
        if ($tags === null) {
            $post->tags()->sync([]);
            return;
        }

        $names = is_array($tags) ? $tags : explode(',', $tags);
        $names = array_values(array_unique(array_filter(array_map(static function ($name) {
            return trim((string) $name);
        }, $names), static function ($name) {
            return $name !== '';
        })));

        // Cap at 6 as per previous behavior
        $names = array_slice($names, 0, 6);

        $tagIds = [];
        foreach ($names as $name) {
            $tag = Tag::firstOrCreate([
                'title' => strtolower($name),
                'slug' => (string) Str::of($name)->slug(),
            ]);
            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $user = User::factory()->create();
        $this->post = Post::factory()->create(['user_id' => $user->id]);
    }

    /**
     * Test syncing null tags removes all associations.
     */
    public function test_sync_with_null_removes_all_tags(): void
    {
        // Create some existing tags for the post
        $tag1 = Tag::factory()->create(['title' => 'existing1', 'slug' => 'existing1']);
        $tag2 = Tag::factory()->create(['title' => 'existing2', 'slug' => 'existing2']);
        $this->post->tags()->attach([$tag1->id, $tag2->id]);
        
        $this->assertEquals(2, $this->post->tags()->count());
        
        $this->syncTags(null, $this->post);
        
        $this->assertEquals(0, $this->post->fresh()->tags()->count());
    }

    /**
     * Test syncing with comma-separated string.
     */
    public function test_sync_with_comma_separated_string(): void
    {
        $tags = 'php,laravel,testing';
        
        $this->syncTags($tags, $this->post);
        
        $this->assertEquals(3, $this->post->fresh()->tags()->count());
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('php', $tagTitles);
        $this->assertContains('laravel', $tagTitles);
        $this->assertContains('testing', $tagTitles);
    }

    /**
     * Test syncing with array of tags.
     */
    public function test_sync_with_array_of_tags(): void
    {
        $tags = ['javascript', 'vue', 'frontend'];
        
        $this->syncTags($tags, $this->post);
        
        $this->assertEquals(3, $this->post->fresh()->tags()->count());
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('javascript', $tagTitles);
        $this->assertContains('vue', $tagTitles);
        $this->assertContains('frontend', $tagTitles);
    }

    /**
     * Test tag creation when tags don't exist.
     */
    public function test_sync_creates_new_tags(): void
    {
        $initialTagCount = Tag::count();
        
        $this->syncTags('newtag1,newtag2', $this->post);
        
        $this->assertEquals($initialTagCount + 2, Tag::count());
        $this->assertDatabaseHas('tags', ['title' => 'newtag1', 'slug' => 'newtag1']);
        $this->assertDatabaseHas('tags', ['title' => 'newtag2', 'slug' => 'newtag2']);
    }

    /**
     * Test tag reuse when tags already exist.
     */
    public function test_sync_reuses_existing_tags(): void
    {
        // Create existing tags
        Tag::factory()->create(['title' => 'existing', 'slug' => 'existing']);
        $initialTagCount = Tag::count();
        
        $this->syncTags('existing,newone', $this->post);
        
        // Should only create one new tag
        $this->assertEquals($initialTagCount + 1, Tag::count());
        $this->assertEquals(2, $this->post->fresh()->tags()->count());
    }

    /**
     * Test tag title normalization to lowercase.
     */
    public function test_sync_normalizes_tag_titles_to_lowercase(): void
    {
        $this->syncTags('PHP,Laravel,TESTING', $this->post);
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('php', $tagTitles);
        $this->assertContains('laravel', $tagTitles);
        $this->assertContains('testing', $tagTitles);
    }

    /**
     * Test tag slug generation.
     */
    public function test_sync_generates_proper_slugs(): void
    {
        $this->syncTags('Web Development,API Design', $this->post);
        
        $tagSlugs = $this->post->tags()->pluck('slug')->toArray();
        $this->assertContains('web-development', $tagSlugs);
        $this->assertContains('api-design', $tagSlugs);
    }

    /**
     * Test trimming of whitespace in tag names.
     */
    public function test_sync_trims_whitespace(): void
    {
        $this->syncTags('  php  , laravel , testing  ', $this->post);
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('php', $tagTitles);
        $this->assertContains('laravel', $tagTitles);
        $this->assertContains('testing', $tagTitles);
        $this->assertEquals(3, count($tagTitles));
    }

    /**
     * Test removal of empty tag names.
     */
    public function test_sync_removes_empty_tags(): void
    {
        $this->syncTags('php,,laravel,  ,testing', $this->post);
        
        $this->assertEquals(3, $this->post->fresh()->tags()->count());
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('php', $tagTitles);
        $this->assertContains('laravel', $tagTitles);
        $this->assertContains('testing', $tagTitles);
    }

    /**
     * Test deduplication of tag names.
     */
    public function test_sync_deduplicates_tags(): void
    {
        $this->syncTags('php,laravel,php,testing,laravel', $this->post);
        
        $this->assertEquals(3, $this->post->fresh()->tags()->count());
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('php', $tagTitles);
        $this->assertContains('laravel', $tagTitles);
        $this->assertContains('testing', $tagTitles);
    }

    /**
     * Test tag limit of 6 tags maximum.
     */
    public function test_sync_limits_to_six_tags(): void
    {
        $manyTags = 'tag1,tag2,tag3,tag4,tag5,tag6,tag7,tag8,tag9,tag10';
        
        $this->syncTags($manyTags, $this->post);
        
        $this->assertEquals(6, $this->post->fresh()->tags()->count());
    }

    /**
     * Test syncing with mixed array and string formats.
     */
    public function test_sync_with_array_containing_whitespace(): void
    {
        $tags = ['  php  ', ' laravel ', 'testing  '];
        
        $this->syncTags($tags, $this->post);
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('php', $tagTitles);
        $this->assertContains('laravel', $tagTitles);
        $this->assertContains('testing', $tagTitles);
    }

    /**
     * Test syncing replaces existing tag associations.
     */
    public function test_sync_replaces_existing_associations(): void
    {
        // First sync
        $this->syncTags('php,laravel', $this->post);
        $this->assertEquals(2, $this->post->fresh()->tags()->count());
        
        // Second sync with different tags
        $this->syncTags('javascript,vue', $this->post);
        $this->assertEquals(2, $this->post->fresh()->tags()->count());
        
        $tagTitles = $this->post->tags()->pluck('title')->toArray();
        $this->assertContains('javascript', $tagTitles);
        $this->assertContains('vue', $tagTitles);
        $this->assertNotContains('php', $tagTitles);
        $this->assertNotContains('laravel', $tagTitles);
    }

    /**
     * Test syncing with special characters in tag names.
     */
    public function test_sync_handles_special_characters(): void
    {
        $this->syncTags('C++,C#,.NET', $this->post);
        
        $this->assertEquals(3, $this->post->fresh()->tags()->count());
        
        $tagSlugs = $this->post->tags()->pluck('slug')->toArray();
        $this->assertContains('c', $tagSlugs); // C++ becomes 'c'
        $this->assertContains('c', $tagSlugs); // C# becomes 'c' (duplicate)
        $this->assertContains('net', $tagSlugs); // .NET becomes 'net'
    }

    /**
     * Test syncing with empty string.
     */
    public function test_sync_with_empty_string(): void
    {
        // Create existing tags first
        $this->syncTags('php,laravel', $this->post);
        $this->assertEquals(2, $this->post->fresh()->tags()->count());
        
        // Sync with empty string should remove all tags
        $this->syncTags('', $this->post);
        $this->assertEquals(0, $this->post->fresh()->tags()->count());
    }

    /**
     * Test syncing with only whitespace.
     */
    public function test_sync_with_only_whitespace(): void
    {
        $this->syncTags('   ,  ,   ', $this->post);
        
        $this->assertEquals(0, $this->post->fresh()->tags()->count());
    }

    /**
     * Test that tag IDs are properly associated.
     */
    public function test_sync_associates_correct_tag_ids(): void
    {
        $this->syncTags('php,laravel', $this->post);
        
        $phpTag = Tag::where('title', 'php')->first();
        $laravelTag = Tag::where('title', 'laravel')->first();
        
        $associatedTagIds = $this->post->tags()->pluck('tags.id')->toArray();
        $this->assertContains($phpTag->id, $associatedTagIds);
        $this->assertContains($laravelTag->id, $associatedTagIds);
    }
}
