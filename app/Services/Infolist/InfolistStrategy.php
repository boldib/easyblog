<?php

namespace App\Services\Infolist;

/**
 * Abstract base class for infolist rendering strategies.
 * 
 * Defines the contract for rendering different types of information
 * lists in the sidebar sections of the application.
 */
abstract class InfolistStrategy
{
    /**
     * Render the infolist content.
     *
     * @param int $num Number of items to display
     * @return void
     */
    abstract public function render(int $num): void;
}
