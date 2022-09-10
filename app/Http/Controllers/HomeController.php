<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        $posts = Post::orderByDesc('id')->paginate(10);
        $users = User::orderByDesc('id')->paginate(20);
        $tags = Tag::orderByDesc('id')->paginate(10);
        $comments = Comment::orderByDesc('id')->paginate(10);

        $allposts = Post::count();
        
        return view('home', compact('posts', 'users', 'tags', 'comments', 'allposts'));
    }
}
