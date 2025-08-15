<?php

namespace App\Repositories;

use App\Classes\Imgstore;
use App\Classes\Tagpost;
use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Eloquent implementation of the PostRepositoryInterface.
 */
class PostRepository implements PostRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function show(string $profileSlug, string $postSlug): Post
    {
        $profile = Profile::where('slug', $profileSlug)->firstOrFail();

        return Post::where('user_id', $profile->user->id)
            ->where('slug', $postSlug)
            ->firstOrFail();
    }

    /**
     * {@inheritdoc}
     */
    public function create(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function store(Request $request): string
    {
        $data = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10|max:50000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024|dimensions:max_width=2000,max_height=2000',
            'tags' => ['nullable', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s,.-]+$/'],
        ]);

        // Generate unique slug
        $baseSlug = Str::of($data['title'])->slug();
        $slug = $baseSlug;
        $counter = 1;
        
        // Check for slug uniqueness and append counter if needed
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $post = Post::create([
            'user_id' => (int) Auth::id(),
            'title' => strip_tags(trim($data['title'])),
            'content' => strip_tags(trim($data['content'])),
            'image' => Imgstore::setPostImage($request->file('image')),
            'slug' => $slug,
        ]);

        Tagpost::sync($data['tags'] ?? null, $post);

        return '/' . $post->user->profile->slug . '/' . $post->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function edit(int $postId): Post
    {
        return Post::where('id', $postId)->firstOrFail();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Request $request, int $postId): Post
    {
        $post = Post::where('id', $postId)->firstOrFail();

        $data = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10|max:50000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024|dimensions:max_width=2000,max_height=2000',
            'tags' => ['nullable', 'string', 'max:150'],
        ]);

        // Generate unique slug if title changed
        $newSlug = Str::of($data['title'])->slug();
        if ($newSlug !== $post->slug) {
            $baseSlug = $newSlug;
            $counter = 1;
            
            // Check for slug uniqueness and append counter if needed
            while (Post::where('slug', $newSlug)->where('id', '!=', $postId)->exists()) {
                $newSlug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $post->update([
            'title' => strip_tags(trim($data['title'])), // Sanitization
            'content' => strip_tags(trim($data['content'])), // Sanitization
            'image' => $request->hasFile('image') ? Imgstore::setPostImage($request->file('image')) : $post->image,
            'slug' => $newSlug,
        ]);

        // Update tags
        Tagpost::sync($data['tags'] ?? null, $post);

        return $post;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $postId, int $authId): bool
    {
        $post = Post::where('id', $postId)->firstOrFail();

        if ($authId !== (int) $post->user->id) {
            abort(403);
        }

        $post->delete();

        return true;
    }
}
