<?php

declare(strict_types=1);

namespace App\View\Components\Sidebar;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * Latest Comments Sidebar Component
 *
 * Displays the most recent comments with clickable links to users and posts.
 * Includes defensive error handling for deleted relationships.
 *
 * @package App\View\Components\Sidebar
 */
class LatestComments extends Component
{
    /**
     * Maximum number of comments to display.
     */
    private const COMMENTS_LIMIT = 10;

    /**
     * Collection of latest comments with user and post relationships.
     */
    public Collection $comments;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->comments = Comment::with([
                'user.profile:id,user_id,slug',
                'user:id,name',
                'post:id,title,slug,user_id',
                'post.user.profile:id,user_id,slug',
                'post.user:id,name'
            ])
            ->whereHas('user') // Only comments with existing users
            ->whereHas('post') // Only comments with existing posts
            ->latest()
            ->take(self::COMMENTS_LIMIT)
            ->get(['id', 'comment', 'user_id', 'post_id', 'created_at']);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View The component view
     */
    public function render(): View
    {
        return view('components.sidebar.latest-comments');
    }
}
