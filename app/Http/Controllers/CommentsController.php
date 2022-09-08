<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function create(Request $request){

        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
        
        $user = auth()->user();

        // Comment Limit by User
        $comments = Comment::where('user_id', $user->id)->where('created_at', '>=', date('Y-m-d').' 00:00:00')->count();
        
        if($comments >= 10){
            $error = 'You have been reached your daily comment limit.';
			return view('error', compact('error'));
        }

        $post = Post::where('id', $request->id)->firstOrFail();
        $comment = new Comment;
        $data = request()->validate(['comment' => 'required']);

        $comment->comment = $request->comment;
        $comment->user_id = $user->id;
        $comment->post_id = $post->id;
        $comment->save();

        return redirect('/'.$post->user->profile->slug.'/'.$post->slug);
       
    }

    public function edit($id){    
    
        $comment = Comment::where('id', $id)->firstOrFail();
        $user = User::where('id', $comment->user_id)->firstOrFail();

        if(auth()->user()->id == $comment->user_id || auth()->user()->role_id == 1){
            return view('comments.edit', compact('comment'));    
        } else{
            abort(403);
        }

    } 

    public function update(Request $request, $id){
        
        $data = request()->validate([	
            'comment' => 'required'
        ]);
    
        $comment = Comment::where('id', $id)->firstOrFail();
        $post = Post::where('id', $comment->post_id)->firstOrFail();
        $user = User::where('id', $post->user_id)->firstOrFail();
    
        if(auth()->user()->id == $comment->user_id || auth()->user()->role == 'admin'){
            
            $comment->comment = $data['comment'];
            $comment->save();
            return redirect('/'.$post->user->profile->slug.'/'.$post->slug);

        } else{
            abort(403);
        }

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
