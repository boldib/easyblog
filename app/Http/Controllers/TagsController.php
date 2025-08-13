<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;

class TagsController extends Controller
{
    /**
     * Display posts for a given tag slug; redirect home if no posts.
     */
    public function index(string $slug): ViewContract|RedirectResponse
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        if ($tag->posts()->count() === 0) {
            return redirect('/');
        }

        $posts = $tag->posts()->orderByDesc('id')->paginate(5);

        return view('posts.tag', compact('tag', 'posts'));
    }
}
