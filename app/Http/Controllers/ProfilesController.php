<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Profile;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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

        if($profile->user->id != Auth::user()->id) abort(403);

        return view('profiles.edit', compact('profile'));
        
    }


    public function update(Request $request, $profileslug)
    {
        $profile = Profile::where('slug', $profileslug)->firstOrFail();
        
        if($profile->user->id != Auth::user()->id) abort(403);

        // authorize protect 
    	if(Auth::user()->id == $profile->user->id || Auth::user()->role == 'admin'){
    	
	    	$data = request()->validate([
				'name'	=> ['required', 'max:32'],
	    		'description' => 'nullable|max:10000',
	    		'slug' => ['required', 'max:32'],
                   'image' => 'nullable|image|max:1024',		
	    	]);
	    		
	    	
	    	if($request->file('image')){	
				$image = Image::make(request('image'))->encode('webp', 100)->fit(400, 400)->save();
			    $imgfilename = uniqid().'.webp';
            
			    Storage::disk('local')->put('public/profiles/'.$imgfilename, fopen($request->file('image'), 'r+'));
				$profile->image = $imgfilename;
	    	}
			
			// DESCRIPTION
			if($data['description']){
			    $profile->description = $data['description'];
			} else {
				$profile->description = NULL;
			}
	    	

			// URL UPDATE
			$urlinput = Str::of($data['slug'])->slug();

			$forbidden = array("admin", "search", "terms-of-service", "tags");

			if(in_array($urlinput, $forbidden)) {return redirect()->back()->withErrors(['slug' => 'This url is already used']);}

            // Check if url-slug is already used by another user:
            $urlExists = Profile::where('slug', $urlinput)->first();
			if(isset($urlExists->slug)){
                if($urlExists->user->id != Auth::user()->id) {return redirect()->back()->withErrors(['slug' => 'This url is already used']);}
			}

			$profile->slug = $urlinput;
			$profile->user->name = $data['name'];

			$profile->update();
			$profile->user->update();

	    	return redirect("/{$profile->slug}");
    	} else abort(403);
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