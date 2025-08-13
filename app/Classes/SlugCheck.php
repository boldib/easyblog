<?php

namespace App\Classes;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Validate and check availability of slugs.
 */
class SlugCheck
{
    /**
     * Reserved slugs that cannot be used.
     */
    private const FORBIDDEN = [
        'admin',
        'search',
        'terms-of-service',
        'tos',
        'tags',
    ];

    /**
     * The raw word to be slugified and validated.
     */
    public string $word;

    public function __construct(string $word)
    {
        $this->word = $word;
    }

    /**
     * Determine if the slugified word is forbidden.
     */
    public function isForbidden(): bool
    {
        $slug = (string) Str::of($this->word)->slug();

        return in_array($slug, self::FORBIDDEN, true);
    }

    /**
     * Determine if the slug is already used by another user's profile.
     */
    public function isUsed(): bool
    {
        $slug = (string) Str::of($this->word)->slug();
        $profile = Profile::where('slug', $slug)->first();

        if ($profile === null) {
            return false;
        }

        // If unauthenticated, consider slug used if it exists at all
        if (! Auth::check()) {
            return true;
        }

        return $profile->user->id !== Auth::id();
    }
}
