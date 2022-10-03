<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Interfaces\ProfileRepositoryInterface;

class ProfilesController extends Controller
{
    private ProfileRepositoryInterface $profileRepository;
    
    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function show($profileSlug)
    {
        $profile = $this->profileRepository->getProfile($profileSlug);
        $posts = $this->profileRepository->getProfilePosts($profile);
        return view('profiles.show', compact('profile', 'posts'));
    }

    public function edit($profileSlug)
    {
        $profile = $this->profileRepository->getProfile($profileSlug);
        return view('profiles.edit', compact('profile'));
    }

    public function update(Request $request, $profileSlug)
    {
        $profile = $this->profileRepository->update($request, $profileSlug, Auth::user());
        return redirect("/{$profile->slug}");
    }

    public function delete($id)
    {
        $delete = $this->profileRepository->delete($id, Auth::user());
    }
}

