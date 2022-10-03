<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Profile;

use App\Classes\Imgstore;
use App\Classes\Tagpost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostsController extends Controller
{

    public function create()
    {
        $user = auth()->user();
        return view('posts.create', compact('user'));
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|max:1024',
            'tags' => ['nullable', 'max:150'],
        ]);        
        
        $post = Post::create([
            'user_id' => Auth::id(),
			'title' => $data['title'],
			'content' => $data['content'],
            'image' => Imgstore::setPostImage($request->file('image')),
			'slug' => Str::of($data['title'])->slug(),
		]);

        Tagpost::sync($data['tags'], $post);

        return redirect("/".$post->user->profile->slug."/".$post->slug);
    }

    public function show($profileslug, $postslug)
    {
        $profile = Profile::where('slug', $profileslug)->firstOrFail();
        
        $post = Post::where('user_id', $profile->user->id)
        ->where('slug', $postslug)
        ->firstOrFail();

        return view('posts.show', compact('post'));
    }
}
