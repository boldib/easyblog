<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewContract;

class SearchController extends Controller
{
    /**
     * Search posts by a query string against title and content.
     */
    public function index(Request $request): ViewContract
    {
        $search = $request->input('s');

        $posts = Post::query()
            ->where('title', 'LIKE', '%' . $search . '%')
            ->orWhere('content', 'LIKE', '%' . $search . '%')
            ->orderByDesc('id')
            ->paginate(10);


        return view('posts.search', compact('posts'));
    }
}
