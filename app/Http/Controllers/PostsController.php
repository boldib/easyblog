<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\Comment;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        return view('posts.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($profileslug, $postslug)
    {
        $profile = Profile::where('slug', $profileslug)->firstOrFail();
        
        $post = Post::where('user_id', $profile->user->id)
        ->where('slug', $postslug)
        ->firstOrFail();

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
