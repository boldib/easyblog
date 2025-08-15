<?php

namespace App\Interfaces;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProfileRepositoryInterface
{
    /**
     * Get a profile by its slug.
     */
    public function getProfile(string $profileSlug): Profile;

    /**
     * Get a profile by its ID.
     */
    public function getProfileById(int $profileId): Profile;

    /**
     * Get paginated posts for a profile.
     */
    public function getProfilePosts(Profile $profile): LengthAwarePaginator;

    /**
     * Update a profile.
     */
    public function update(Request $request, string $profileSlug, User $auth): Profile;

    /**
     * Delete a profile.
     */
    public function delete(int $id, User $auth): bool;
}
