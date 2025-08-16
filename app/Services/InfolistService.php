<?php

namespace App\Services;

use App\Models\Post;
use App\Services\Infolist\Strategies\UsersInfolistStrategy;
use App\Services\Infolist\Strategies\TagsInfolistStrategy;
use App\Services\Infolist\Strategies\CommentsInfolistStrategy;
use App\Services\Infolist\InfolistStrategy;
use InvalidArgumentException;

/**
 * Service for rendering information lists in sidebar sections.
 * 
 * This service uses the Strategy pattern to handle different types
 * of information lists (users, tags, comments) in a maintainable way.
 */
class InfolistService
{
    /**
     * Available strategy mappings.
     *
     * @var array<string, class-string<InfolistStrategy>>
     */
    private array $strategies = [
        'users' => UsersInfolistStrategy::class,
        'tags' => TagsInfolistStrategy::class,
        'comments' => CommentsInfolistStrategy::class,
    ];

    /**
     * Render small info lists for sidebar sections.
     *
     * Echoes small HTML snippets for users, tags, and comments.
     *
     * @param string $type The type of infolist to render (users, tags, comments)
     * @param int $num Number of items to display
     * @return void
     * @throws InvalidArgumentException When an invalid type is provided
     */
    public function render(string $type, int $num): void
    {
        if (!isset($this->strategies[$type])) {
            throw new InvalidArgumentException("Invalid infolist type: {$type}");
        }

        $strategyClass = $this->strategies[$type];
        $strategy = new $strategyClass();
        $strategy->render($num);
    }

    /**
     * Get available infolist types.
     *
     * @return array<string>
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->strategies);
    }

    /**
     * Register a new strategy.
     *
     * @param string $type
     * @param class-string<InfolistStrategy> $strategyClass
     * @return void
     */
    public function registerStrategy(string $type, string $strategyClass): void
    {
        $this->strategies[$type] = $strategyClass;
    }

    // Backward compatibility static methods
    // These will be deprecated in future versions
    
    /**
     * @deprecated Use dependency injection instead
     */
    public static function get(string $type, int $num): void
    {
        $service = new self();
        $service->render($type, $num);
    }

    /**
     * Get total posts count.
     * @deprecated Use dependency injection instead
     */
    public static function postscount(): int
    {
        return Post::count();
    }
}
