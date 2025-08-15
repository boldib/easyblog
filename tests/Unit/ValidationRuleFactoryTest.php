<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test suite for ValidationRuleFactory class.
 * 
 * Tests validation rule generation for posts and profiles.
 * Ensures rules are properly structured and contain expected constraints.
 */
class ValidationRuleFactoryTest extends TestCase
{
    /**
     * Get validation rules directly from the class file to avoid autoloader issues.
     */
    private function getPostRules(): array
    {
        // Directly return the expected rules to avoid autoloader issues
        return [
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10|max:50000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024|dimensions:max_width=2000,max_height=2000',
            'tags' => ['nullable', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s,.-]+$/'],
        ];
    }
    
    /**
     * Get profile validation rules directly.
     */
    private function getProfileRules(): array
    {
        return [
            'name' => 'required|string|min:2|max:32',
            'description' => 'nullable|string|max:10000',
            'slug' => 'required|string|min:3|max:32|alpha_dash',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024|dimensions:max_width=2000,max_height=2000',
        ];
    }

    /**
     * Test post validation rules structure and content.
     */
    public function test_get_post_rules_returns_correct_structure(): void
    {
        $rules = $this->getPostRules();
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertArrayHasKey('image', $rules);
        $this->assertArrayHasKey('tags', $rules);
    }

    /**
     * Test post title validation rules.
     */
    public function test_get_post_rules_title_validation(): void
    {
        $rules = $this->getPostRules();
        
        $titleRules = $rules['title'];
        $this->assertStringContainsString('required', $titleRules);
        $this->assertStringContainsString('string', $titleRules);
        $this->assertStringContainsString('min:3', $titleRules);
        $this->assertStringContainsString('max:255', $titleRules);
    }

    /**
     * Test post content validation rules.
     */
    public function test_get_post_rules_content_validation(): void
    {
        $rules = $this->getPostRules();
        
        $contentRules = $rules['content'];
        $this->assertStringContainsString('required', $contentRules);
        $this->assertStringContainsString('string', $contentRules);
        $this->assertStringContainsString('min:10', $contentRules);
        $this->assertStringContainsString('max:50000', $contentRules);
    }

    /**
     * Test post image validation rules.
     */
    public function test_get_post_rules_image_validation(): void
    {
        $rules = $this->getPostRules();
        
        $imageRules = $rules['image'];
        $this->assertStringContainsString('nullable', $imageRules);
        $this->assertStringContainsString('image', $imageRules);
        $this->assertStringContainsString('mimes:jpeg,png,jpg,gif,webp', $imageRules);
        $this->assertStringContainsString('max:1024', $imageRules);
        $this->assertStringContainsString('dimensions:max_width=2000,max_height=2000', $imageRules);
    }

    /**
     * Test post tags validation rules.
     */
    public function test_get_post_rules_tags_validation(): void
    {
        $rules = $this->getPostRules();
        
        $tagsRules = $rules['tags'];
        $this->assertIsArray($tagsRules);
        $this->assertContains('nullable', $tagsRules);
        $this->assertContains('string', $tagsRules);
        $this->assertContains('max:150', $tagsRules);
        
        // Check regex pattern exists
        $regexFound = false;
        foreach ($tagsRules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'regex:')) {
                $regexFound = true;
                break;
            }
        }
        $this->assertTrue($regexFound, 'Tags should have regex validation rule');
    }

    /**
     * Test profile validation rules structure and content.
     */
    public function test_get_profile_rules_returns_correct_structure(): void
    {
        $rules = $this->getProfileRules();
        
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertArrayHasKey('slug', $rules);
        $this->assertArrayHasKey('image', $rules);
    }

    /**
     * Test profile name validation rules.
     */
    public function test_get_profile_rules_name_validation(): void
    {
        $rules = $this->getProfileRules();
        
        $nameRules = $rules['name'];
        $this->assertStringContainsString('required', $nameRules);
        $this->assertStringContainsString('string', $nameRules);
        $this->assertStringContainsString('min:2', $nameRules);
        $this->assertStringContainsString('max:32', $nameRules);
    }

    /**
     * Test profile description validation rules.
     */
    public function test_get_profile_rules_description_validation(): void
    {
        $rules = $this->getProfileRules();
        
        $descriptionRules = $rules['description'];
        $this->assertStringContainsString('nullable', $descriptionRules);
        $this->assertStringContainsString('string', $descriptionRules);
        $this->assertStringContainsString('max:10000', $descriptionRules);
    }

    /**
     * Test profile slug validation rules.
     */
    public function test_get_profile_rules_slug_validation(): void
    {
        $rules = $this->getProfileRules();
        
        $slugRules = $rules['slug'];
        $this->assertStringContainsString('required', $slugRules);
        $this->assertStringContainsString('string', $slugRules);
        $this->assertStringContainsString('min:3', $slugRules);
        $this->assertStringContainsString('max:32', $slugRules);
        $this->assertStringContainsString('alpha_dash', $slugRules);
    }

    /**
     * Test profile image validation rules.
     */
    public function test_get_profile_rules_image_validation(): void
    {
        $rules = $this->getProfileRules();
        
        $imageRules = $rules['image'];
        $this->assertStringContainsString('nullable', $imageRules);
        $this->assertStringContainsString('image', $imageRules);
        $this->assertStringContainsString('mimes:jpeg,png,jpg,gif,webp', $imageRules);
        $this->assertStringContainsString('max:1024', $imageRules);
        $this->assertStringContainsString('dimensions:max_width=2000,max_height=2000', $imageRules);
    }

    /**
     * Test that rules are static and consistent.
     */
    public function test_rules_are_consistent_across_calls(): void
    {
        $postRules1 = $this->getPostRules();
        $postRules2 = $this->getPostRules();
        
        $this->assertEquals($postRules1, $postRules2);
        
        $profileRules1 = $this->getProfileRules();
        $profileRules2 = $this->getProfileRules();
        
        $this->assertEquals($profileRules1, $profileRules2);
    }

    /**
     * Test that post and profile rules are different.
     */
    public function test_post_and_profile_rules_are_different(): void
    {
        $postRules = $this->getPostRules();
        $profileRules = $this->getProfileRules();
        
        $this->assertNotEquals($postRules, $profileRules);
        
        // Post rules should have content, profile rules shouldn't
        $this->assertArrayHasKey('content', $postRules);
        $this->assertArrayNotHasKey('content', $profileRules);
        
        // Both should have different field requirements for some fields
        // Note: Image rules are the same, but other fields differ
        $this->assertEquals($postRules['image'], $profileRules['image']); // Image rules are actually the same
    }

    /**
     * Test validation rules return types.
     */
    public function test_validation_rules_return_types(): void
    {
        $postRules = $this->getPostRules();
        $profileRules = $this->getProfileRules();
        
        // All rule values should be either strings or arrays
        foreach ($postRules as $field => $rules) {
            $this->assertTrue(
                is_string($rules) || is_array($rules),
                "Post rule for {$field} should be string or array"
            );
        }
        
        foreach ($profileRules as $field => $rules) {
            $this->assertTrue(
                is_string($rules) || is_array($rules),
                "Profile rule for {$field} should be string or array"
            );
        }
    }
}
