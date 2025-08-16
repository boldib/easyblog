<?php

namespace App\Services\Infolist\Strategies;

use App\Models\Comment;
use App\Services\Infolist\InfolistStrategy;

/**
 * Strategy for rendering comments infolist.
 * 
 * Displays a list of recent comments with links to their respective posts.
 */
class CommentsInfolistStrategy extends InfolistStrategy
{
    /**
     * Render comments infolist.
     *
     * @param int $num Number of comments to display
     * @return void
     */
    public function render(int $num): void
    {
        $comments = Comment::query()->with(['post.user.profile'])->take($num)->get();

        foreach ($comments as $comment) {
            if ($comment->post && $comment->post->user && $comment->post->user->profile) {
                $profileSlug = e($comment->post->user->profile->slug);
                $postSlug = e($comment->post->slug);
                $commentText = e($comment->comment);
                
                echo '<a href="/' . $profileSlug . '/' . $postSlug . '">' 
                    . $commentText . '</a><br>';
            }
        }
    }
}
