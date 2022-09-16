<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class SearchController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('s');

        $posts = Post::query()
            ->where('title', 'LIKE', '%'.$search.'%')
            ->orWhere('content', 'LIKE', '%'.$search.'%')
            ->orderBy('id', 'DESC')
            ->paginate(10);


        return view('posts.search', compact('posts'));
    }
}
