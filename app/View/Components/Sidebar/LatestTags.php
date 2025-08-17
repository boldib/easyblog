<?php

declare(strict_types=1);

namespace App\View\Components\Sidebar;

use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * Latest Tags Sidebar Component
 *
 * Displays recently created tags as clickable badges.
 * Helps users discover and navigate to tag-filtered content.
 *
 * @package App\View\Components\Sidebar
 */
class LatestTags extends Component
{
    /**
     * Maximum number of tags to display.
     */
    private const TAGS_LIMIT = 10;

    /**
     * Collection of recently created tags.
     */
    public Collection $tags;

    /**
     * Create a new component instance.
     *
     * Fetches the most recently created tags for display.
     */
    public function __construct()
    {
        $this->tags = Tag::latest()
            ->take(self::TAGS_LIMIT)
            ->get(['id', 'title', 'slug', 'created_at']);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View The component view
     */
    public function render(): View
    {
        return view('components.sidebar.latest-tags');
    }
}
