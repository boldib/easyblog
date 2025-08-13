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
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:1024',
            'tags' => ['nullable', 'max:150'],
        ]);

        $post = Post::create([
            'user_id' => (int) Auth::id(),
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => Imgstore::setPostImage($request->file('image')),
            'slug' => Str::of($data['title'])->slug(),
        ]);

        Tagpost::sync($data['tags'] ?? null, $post);

        return '/' . $post->user->profile->slug . '/' . $post->slug;
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
