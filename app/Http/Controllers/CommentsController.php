<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class CommentsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('comment.owner')->only(['edit', 'update', 'delete']);
    }

    /**
     * Create a new comment for a post.
     */
    public function create(Request $request): ViewContract|RedirectResponse
    {
        $user = auth()->user();

        // Comment Limit by User
        $comments = Comment::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->count();

        if ($comments >= 10) {
            return redirect()->back()->withErrors([
                'comment' => 'You have reached your daily comment limit of 10 comments. Please try again tomorrow.'
            ]);
        }

        $post = Post::where('id', $request->id)->firstOrFail();

        $data = $request->validate([
            'comment' => 'required|string|min:3|max:1000',
        ]);

        $comment = new Comment();
        $comment->comment = sanitize_required($data['comment']); // Sanitization
        $comment->user_id = $user->id;
        $comment->post_id = $post->id;
        $comment->save();

        return redirect('/' . $post->user->profile->slug . '/' . $post->slug);
    }

    /**
     * Delete a comment by its id.
     */
    public function delete(Request $request, int $id): RedirectResponse
    {
        $comment = Comment::where('id', $id)->firstOrFail();
        $post = Post::where('id', $comment->post_id)->firstOrFail();
        
        $comment->delete();
        
        return redirect('/' . $post->user->profile->slug . '/' . $post->slug)
            ->with('success', 'Comment deleted successfully.');
    }
}
