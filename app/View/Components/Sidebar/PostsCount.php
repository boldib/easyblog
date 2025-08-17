<?php

declare(strict_types=1);

namespace App\View\Components\Sidebar;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Posts Count Sidebar Component
 *
 * Displays the total number of posts in the database.
 * Provides a quick overview of content volume for users.
 *
 * @package App\View\Components\Sidebar
 */
class PostsCount extends Component
{
    /**
     * The total number of posts in the database.
     */
    public int $count;

    /**
     * Create a new component instance.
     *
     * Fetches the total post count from the database.
     */
    public function __construct()
    {
        $this->count = Post::count();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View The component view
     */
    public function render(): View
    {
        return view('components.sidebar.posts-count');
    }
}
