<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\View\Component;

/**
 * Sidebar view component.
 */
class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Intentionally left blank. Add dependencies via constructor if needed.
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return ViewContract|Closure|string
     */
    public function render(): ViewContract|Closure|string
    {
        return view('components.sidebar');
    }
}
