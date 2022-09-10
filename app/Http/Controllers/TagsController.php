<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index($slug){
        
        $tag = Tag::where('slug', $slug)->firstOrFail();

        if($tag->posts()->count() == 0){
            return redirect('/');
        }
            
        $posts = $tag->posts()->orderBy('id', 'DESC')->paginate(5);

        return view('posts.tag', compact('tag', 'posts'));

        

    }
}
