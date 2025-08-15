<?php

namespace App\Classes;

class ValidationRuleFactory
{
    /**
     * Get validation rules for post creation/update.
     */
    public static function getPostRules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:10|max:50000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024|dimensions:max_width=2000,max_height=2000',
            'tags' => ['nullable', 'string', 'max:150', 'regex:/^[a-zA-Z0-9\s,.-]+$/'],
        ];
    }

    /**
     * Get validation rules for profile update.
     */
    public static function getProfileRules(): array
    {
        return [
            'name' => 'required|string|min:2|max:32',
            'description' => 'nullable|string|max:10000',
            'slug' => 'required|string|min:3|max:32|alpha_dash',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024|dimensions:max_width=2000,max_height=2000',
        ];
    }
}
