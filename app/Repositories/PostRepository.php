<?php

namespace App\Repositories;

use App\Classes\Tagpost;
use App\Classes\ValidationRuleFactory;
use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Services\ImageService;
use App\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $data = $request->validate(ValidationRuleFactory::getPostRules());

        // Generate unique slug using service
        $slug = SlugService::generateUniquePostSlug($data['title']);

        $post = Post::create([
            'user_id' => (int) Auth::id(),
            'title' => strip_tags(trim($data['title'])),
            'content' => strip_tags(trim($data['content'])),
            'image' => ImageService::setPostImage($request->file('image')),
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

        $data = $request->validate(ValidationRuleFactory::getPostRules());

        // Generate unique slug if title changed using service
        $newSlug = SlugService::generateUniquePostSlug($data['title'], $postId);

        $post->update([
            'title' => strip_tags(trim($data['title'])),
            'content' => strip_tags(trim($data['content'])),
            'image' => $request->hasFile('image') ? ImageService::setPostImage($request->file('image')) : $post->image,
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

        AuthorizationService::canModifyPost((int) $post->user->id, $authId);

        $post->delete();

        return true;
    }
}
