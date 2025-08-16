<?php

namespace App\Services\Infolist\Strategies;

use App\Models\User;
use App\Services\Infolist\InfolistStrategy;

/**
 * Strategy for rendering users infolist.
 * 
 * Displays a list of users with their profile images and names,
 * linking to their profile pages.
 */
class UsersInfolistStrategy extends InfolistStrategy
{
    /**
     * Render users infolist.
     *
     * @param int $num Number of users to display
     * @return void
     */
    public function render(int $num): void
    {
        $users = User::query()->with('profile')->take($num)->get();

        foreach ($users as $user) {
            if ($user->profile && $user->profile->slug) {
                $imageUrl = method_exists($user->profile, 'image') 
                    ? $user->profile->image() 
                    : '/images/default-avatar.png';
                $profileSlug = e($user->profile->slug);
                $userName = e($user->name);
                
                echo '<a href="/' . $profileSlug . '">'
                    . '<img src="' . $imageUrl . '" alt="' . $userName . '" '
                    . 'style="width:20px;height:20px;border-radius:50%;"> '
                    . $userName . '</a><br>';
            }
        }
    }
}
