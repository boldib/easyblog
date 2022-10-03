<?php

namespace App\Interfaces;
use Illuminate\Http\Request;

interface ProfileRepositoryInterface
{
    public function getProfile($profileSlug);
    public function getProfileById(int $profileId);
    public function getProfilePosts(Profile $profile);
    public function update(Request $request, $profileSlug, Auth $auth);
    public function delete($id, Auth $auth);
}