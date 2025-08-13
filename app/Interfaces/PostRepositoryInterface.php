<?php

namespace App\Interfaces;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Contract for post repository operations.
 */
interface PostRepositoryInterface
{
    /**
     * Retrieve a post by owner profile slug and post slug.
     */
    public function show(string $profileSlug, string $postSlug): Post;

    /**
     * Get the currently authenticated user for create form context.
     */
    public function create(): User;

    /**
     * Persist a newly created post and return a redirect path.
     */
    public function store(Request $request): string;

    /**
     * Delete a post by id if the authenticated user is the owner.
     */
    public function delete(int $postId, int $authId): bool;
}
