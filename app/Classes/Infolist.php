<?php 
namespace App\Classes;

use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;

class Infolist
{
    public static function get(string $type, int $num){

        if($type == 'users'){
            
            $users = User::all()->take($num);

            foreach($users as $user){
                echo '<a href="/'.$user->profile->slug.'"><img class="rounded-circle m-1" width="20px" height="20px" src="'.$user->profile->image().'">'.$user->name.'</a><br>';
            }

        }

        if($type == 'tags'){

            $tags = Tag::all()->take($num);
        
            for ($i=0; $i < $num ; $i++) { 
                echo '<a href="/tag/'.$tags[$i]->slug.'">'.$tags[$i]->title.'</a><br>';
            }

        }

        if($type == 'comments'){

            $comments = Comment::all()->take($num);

            foreach($comments as $comment){
                echo '<a href="/'.$comment->post->user->profile->slug.'/'.$comment->post->slug.'">'.$comment->comment.'</a><br>';
            }

        }
        

    }

    
        

    
}