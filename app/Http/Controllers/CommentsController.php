<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class CommentsController extends Controller
{
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
            $error = 'You have been reached your daily comment limit.';
            return view('error', compact('error'));
        }

        $post = Post::where('id', $request->id)->firstOrFail();

        $data = $request->validate(['comment' => 'required']);

        $comment = new Comment();
        $comment->comment = $data['comment'];
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
        $user = User::where('id', $post->user_id)->firstOrFail();

        if (auth()->user()->id == $comment->user_id || auth()->user()->role == 'admin') {
            $comment->delete();
            return redirect('/' . $post->user->profile->slug . '/' . $post->slug);
        }

        return redirect('/');
    }
}
