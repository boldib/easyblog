<?php

declare(strict_types=1);

namespace App\View\Components\Sidebar;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * New Users Sidebar Component
 *
 * Displays recently registered users with clickable links to their profiles.
 * Provides quick access to discover new community members.
 *
 * @package App\View\Components\Sidebar
 */
class NewUsers extends Component
{
    /**
     * Maximum number of users to display.
     */
    private const USERS_LIMIT = 15;

    /**
     * Collection of recently registered users with profile relationships.
     */
    public Collection $users;

    /**
     * Create a new component instance.
     *
     * Fetches the most recently registered users with their profile data.
     */
    public function __construct()
    {
        $this->users = User::with('profile:id,user_id,slug')
            ->latest()
            ->take(self::USERS_LIMIT)
            ->get(['id', 'name', 'created_at']);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View The component view
     */
    public function render(): View
    {
        return view('components.sidebar.new-users');
    }
}
