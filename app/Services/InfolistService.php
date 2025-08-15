<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\Post;

abstract class InfolistStrategy
{
    abstract public function render(int $num): void;
}

class UsersInfolistStrategy extends InfolistStrategy
{
    public function render(int $num): void
    {
        $users = User::query()->with('profile')->take($num)->get();

        foreach ($users as $user) {
            if ($user->profile && $user->profile->slug) {
                $imageUrl = method_exists($user->profile, 'image') ? $user->profile->image() : '/images/default-avatar.png';
                $profileSlug = htmlspecialchars($user->profile->slug, ENT_QUOTES, 'UTF-8');
                $userName = htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8');
                
                echo '<a href="/' . $profileSlug . '"><img src="' . $imageUrl . '" alt="' . $userName . '" style="width:20px;height:20px;border-radius:50%;"> ' . $userName . '</a><br>';
            }
        }
    }
}

class TagsInfolistStrategy extends InfolistStrategy
{
    public function render(int $num): void
    {
        $tags = Tag::query()->take($num)->get();

        foreach ($tags as $tag) {
            $tagSlug = htmlspecialchars($tag->slug, ENT_QUOTES, 'UTF-8');
            $tagTitle = htmlspecialchars($tag->title, ENT_QUOTES, 'UTF-8');
            
            echo '<a href="/tag/' . $tagSlug . '">' . $tagTitle . '</a><br>';
        }
    }
}

class CommentsInfolistStrategy extends InfolistStrategy
{
    public function render(int $num): void
    {
        $comments = Comment::query()->with(['post.user.profile'])->take($num)->get();

        foreach ($comments as $comment) {
            if ($comment->post && $comment->post->user && $comment->post->user->profile) {
                $profileSlug = htmlspecialchars($comment->post->user->profile->slug, ENT_QUOTES, 'UTF-8');
                $postSlug = htmlspecialchars($comment->post->slug, ENT_QUOTES, 'UTF-8');
                $commentText = htmlspecialchars($comment->comment, ENT_QUOTES, 'UTF-8');
                
                echo '<a href="/' . $profileSlug . '/' . $postSlug . '">' . $commentText . '</a><br>';
            }
        }
    }
}

class InfolistService
{
    private static array $strategies = [
        'users' => UsersInfolistStrategy::class,
        'tags' => TagsInfolistStrategy::class,
        'comments' => CommentsInfolistStrategy::class,
    ];

    /**
     * Render small info lists for sidebar sections.
     *
     * Echoes small HTML snippets for users, tags, and comments.
     */
    public static function get(string $type, int $num): void
    {
        if (!isset(self::$strategies[$type])) {
            return;
        }

        $strategy = new self::$strategies[$type]();
        $strategy->render($num);
    }

    /**
     * Get total posts count.
     */
    public static function postscount(): int
    {
        return Post::count();
    }
}
