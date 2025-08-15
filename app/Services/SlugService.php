<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Generate a unique slug for a post.
     */
    public static function generateUniquePostSlug(string $title, ?int $excludeId = null): string
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
}
