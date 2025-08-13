<?php

namespace App\Classes;

use App\Models\Post;
use App\Models\Tag;

use Illuminate\Support\Str;

class Tagpost
{
    /**
     * Sync tags to a post, creating tags as needed.
     *
     * Accepts a comma-separated string or array of names. Names are trimmed,
     * lowercased for title, slugified for slug, de-duplicated, and capped at 6.
     */
    public static function sync(string|array|null $tags, Post $post): void
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
}
