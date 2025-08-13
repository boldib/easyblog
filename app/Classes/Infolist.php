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
            $users = User::query()->take($num)->get();

            foreach ($users as $user) {
                echo '<a href="/' . $user->profile->slug . '"><img class="rounded-circle m-1" width="20px" height="20px" src="' . $user->profile->image() . '">' . $user->name . '</a><br>';
            }
        }

        if ($type === 'tags') {
            $tags = Tag::query()->take($num)->get();

            $count = min($num, $tags->count());
            for ($i = 0; $i < $count; $i++) {
                echo '<a href="/tag/' . $tags[$i]->slug . '">' . $tags[$i]->title . '</a><br>';
            }
        }

        if ($type === 'comments') {
            $comments = Comment::query()->latest()->take($num)->get();

            foreach ($comments as $comment) {
                echo '<a href="/' . $comment->post->user->profile->slug . '/' . $comment->post->slug . '">' . $comment->comment . '</a><br>';
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
