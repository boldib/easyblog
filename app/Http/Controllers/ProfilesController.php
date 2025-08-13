<?php

namespace App\Http\Controllers;

use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class ProfilesController extends Controller
{
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Display a profile and its posts.
     */
    public function show(string $profileSlug): ViewContract
    {
        $profile = $this->profileRepository->getProfile($profileSlug);
        $posts = $this->profileRepository->getProfilePosts($profile);
        return view('profiles.show', compact('profile', 'posts'));
    }

    /**
     * Show the edit form for a profile.
     */
    public function edit(string $profileSlug): ViewContract
    {
        $profile = $this->profileRepository->getProfile($profileSlug);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }
        
        // Check if the current user owns this profile
        $currentUserId = Auth::id();
        if ($currentUserId != $profile->user_id) {
            abort(403, "Unauthorized to edit this profile. User ID: {$currentUserId}, Profile User ID: {$profile->user_id}");
        }
        
        return view('profiles.edit', compact('profile'));
    }

    /**
     * Update the profile.
     */
    public function update(Request $request, string $profileSlug): RedirectResponse
    {
        $profile = $this->profileRepository->getProfile($profileSlug);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }
        
        // Check if the current user owns this profile
        $currentUserId = Auth::id();
        

        
        if ($currentUserId != $profile->user_id) {
            abort(403, "Unauthorized to update this profile. User ID: {$currentUserId} (" . gettype($currentUserId) . "), Profile User ID: {$profile->user_id} (" . gettype($profile->user_id) . ")");
        }
        
        $updatedProfile = $this->profileRepository->update($request, $profileSlug, Auth::user());
        return redirect("/{$updatedProfile->slug}");
    }

    /**
     * Delete a profile.
     */
    public function delete(int $id): RedirectResponse
    {
        $this->profileRepository->delete($id, Auth::user());
        return redirect('/');
    }
}
