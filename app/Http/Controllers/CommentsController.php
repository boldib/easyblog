<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function create(Request $request){
        
        $user = auth()->user();

        // Comment Limit by User
        $comments = Comment::where('user_id', $user->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->count();
        
        if($comments >= 10){
            $error = 'You have been reached your daily comment limit.';
			return view('error', compact('error'));
        }

        $post = Post::where('id', $request->id)->firstOrFail();

        $data = request()->validate(['comment' => 'required']);

        $comment = new Comment;
        $comment->comment = $request->comment;
        $comment->user_id = $user->id;
        $comment->post_id = $post->id;
        $comment->save();

        return redirect('/'.$post->user->profile->slug.'/'.$post->slug);
       
    }

    public function delete(Request $request, $id){
    	
        $comment = Comment::where('id', $id)->firstOrFail();
        $post = Post::where('id', $comment->post_id)->firstOrFail();
        $user = User::where('id', $post->user_id)->firstOrFail();
            
        if(auth()->user()->id == $comment->user_id || auth()->user()->role == 'admin'){
            $comment->delete();
            return redirect('/'.$post->user->profile->slug.'/'.$post->slug);
    	}

    	
	}
}
