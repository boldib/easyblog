<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Profile;

use App\Classes\SlugCheck;
use App\Classes\Imgstore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfilesController extends Controller
{

    public function show($profileslug)
    {
        $profile = Profile::where('slug', $profileslug)->firstorFail();
        $posts = Post::where('user_id', $profile->user->id)
            ->orderByDesc('id')
            ->paginate(10);
        return view('profiles.show', compact('profile', 'posts'));
    }


    public function edit($profileslug)
    {
        $profile = Profile::where('slug', $profileslug)->firstOrFail();
        if ($profile->user->id != Auth::user()->id) abort(403);
        return view('profiles.edit', compact('profile'));
    }


    public function update(Request $request, $profileslug)
    {
        $profile = Profile::where('slug', $profileslug)->firstOrFail();
        if ($profile->user->id != Auth::user()->id || Auth::user()->role != 'admin') abort(403);

        $data = request()->validate([
            'name' => ['required', 'max:32'],
            'description' => 'nullable|max:10000',
            'slug' => ['required', 'max:32'],
            'image' => 'nullable|image|max:1024',
        ]);

        $slugCheck = new SlugCheck($data['slug']);
        if ($slugCheck->isForbidden() || $slugCheck->isUsed()) return redirect()->back()->withErrors(['slug' => 'This url is already used']);

        $profile->user->name = $data['name'];
        $profile->slug = Str::of($data['slug'])->slug();
        $profile->image = Imgstore::setProfileImage($request->file('image'));
        $profile->description = (isset($data['description'])) ? $data['description'] : NULL;
        $profile->update();
        $profile->user->update();

        return redirect("/{$profile->slug}");
    }


    public function destroy($id)
    {
        $profile = Profile::where('id', $id)->firstOrFail();
        $user = User::where('id', $profile->user->id)->firstOrFail();

        $user->posts()->delete();
        $user->likes()->delete();
        $user->comments()->delete();
        $user->profile()->delete();
        $user->delete();
    }
}

