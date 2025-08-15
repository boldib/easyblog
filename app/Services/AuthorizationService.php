<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;

class AuthorizationService
{
    /**
     * Check if the authenticated user can modify a profile.
     * 
     * @param Profile $profile The profile to check
     * @param User $auth The authenticated user
     * @return bool
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public static function canModifyProfile(Profile $profile, User $auth): bool
    {
        if ($profile->user->id != $auth->id && $auth->role != 'admin') {
            abort(403);
        }
        
        return true;
    }

    /**
     * Check if the authenticated user can modify a post.
     * 
     * @param int $postOwnerId The ID of the post owner
     * @param int $authId The authenticated user ID
     * @return bool
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public static function canModifyPost(int $postOwnerId, int $authId): bool
    {
        if ($authId !== $postOwnerId) {
            abort(403);
        }
        
        return true;
    }
}
