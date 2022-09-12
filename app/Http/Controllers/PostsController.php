<?php

namespace App\Http\Controllers;

use File;
use App\Models\Post;
use App\Models\User;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\Comment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
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

        // Image
        if($request->file('image')){
			
            $image = Image::make($request->file('image'))->encode('webp', 100)->fit(400, 400)->save();
			$imgfilename = uniqid().'.webp';
            
			Storage::disk('local')->put('public/images/'.$imgfilename, fopen($request->file('image'), 'r+'));

		} else {
            $imgfilename = null;
        }

        // Slug
        $slug = Str::of($data['title'])->slug();
        
        // Store post
        $post = Post::create([
            'user_id' => Auth::id(),
			'title' => $data['title'],
			'content' => $data['content'],
			'image' => $imgfilename,
			'slug' => $slug,
		]);

        //TAGS
		if(isset($data['tags']) && $post){ 
            
            $tagNames = explode(',',$request->get('tags'));

		    $tagIds = [];
		    $tagcount = 0;

		    foreach($tagNames as $tagName){

				$tagcount++;
				
                $tag = Tag::firstOrCreate([
					'title'=> strtolower($tagName),
					'slug' => Str::of($tagName)->slug(),
				]);

				if($tag){
				    $tagIds[] = $tag->id;
				}

				if($tagcount == 6) break;

			}
			
            $post->tags()->sync($tagIds);

		}


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
