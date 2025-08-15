<?php

namespace App\Classes;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

class Infolist
{
    /**
     * Render small info lists for sidebar sections.
     *
     * Echoes small HTML snippets for users, tags, and comments.
     */
    public static function get(string $type, int $num): void
    {
        if ($type === 'users') {
            $users = User::query()->with('profile')->take($num)->get();

            foreach ($users as $user) {
                if ($user->profile && $user->profile->slug) {
                    $imageUrl = method_exists($user->profile, 'image') ? $user->profile->image() : '/images/default-avatar.png';
                    echo '<a href="/' . htmlspecialchars($user->profile->slug, ENT_QUOTES, 'UTF-8') . '"><img class="rounded-circle m-1" width="20px" height="20px" src="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') . '</a><br>';
                }
            }
        }

        if ($type === 'tags') {
            $tags = Tag::query()->take($num)->get();

            $count = min($num, $tags->count());
            for ($i = 0; $i < $count; $i++) {
                if (isset($tags[$i]) && $tags[$i]->slug && $tags[$i]->title) {
                    echo '<a href="/tag/' . htmlspecialchars($tags[$i]->slug, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($tags[$i]->title, ENT_QUOTES, 'UTF-8') . '</a><br>';
                }
            }
        }

        if ($type === 'comments') {
            $comments = Comment::query()
                ->with(['post.user.profile', 'post'])
                ->latest()
                ->take($num)
                ->get();

            foreach ($comments as $comment) {
                if ($comment->post && 
                    $comment->post->user && 
                    $comment->post->user->profile && 
                    $comment->post->user->profile->slug && 
                    $comment->post->slug &&
                    $comment->comment) {
                    
                    $profileSlug = htmlspecialchars($comment->post->user->profile->slug, ENT_QUOTES, 'UTF-8');
                    $postSlug = htmlspecialchars($comment->post->slug, ENT_QUOTES, 'UTF-8');
                    $commentText = htmlspecialchars($comment->comment, ENT_QUOTES, 'UTF-8');
                    
                    echo '<a href="/' . $profileSlug . '/' . $postSlug . '">' . $commentText . '</a><br>';
                }
            }
        }
    }

    /**
     * Get total posts count.
     */
    public static function postscount(): int
    {
        return Post::count();
    }
}
