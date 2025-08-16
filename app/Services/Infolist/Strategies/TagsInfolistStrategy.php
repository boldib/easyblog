<?php

namespace App\Services\Infolist\Strategies;

use App\Models\Tag;
use App\Services\Infolist\InfolistStrategy;

/**
 * Strategy for rendering tags infolist.
 * 
 * Displays a list of tags with links to their respective tag pages.
 */
class TagsInfolistStrategy extends InfolistStrategy
{
    /**
     * Render tags infolist.
     *
     * @param int $num Number of tags to display
     * @return void
     */
    public function render(int $num): void
    {
        $tags = Tag::query()->take($num)->get();

        foreach ($tags as $tag) {
            $tagSlug = e($tag->slug);
            $tagTitle = e($tag->title);
            
            echo '<a href="/tag/' . $tagSlug . '">' . $tagTitle . '</a><br>';
        }
    }
}
