<?php

if (!function_exists('sanitize')) {
    /**
     * Sanitize input by stripping tags and trimming whitespace.
     *
     * @param string|null $value
     * @return string|null
     */
    function sanitize(?string $value): ?string
    {
        return $value !== null ? strip_tags(trim($value)) : null;
    }
}

if (!function_exists('sanitize_required')) {
    /**
     * Sanitize input by stripping tags and trimming whitespace (non-nullable version).
     *
     * @param string $value
     * @return string
     */
    function sanitize_required(string $value): string
    {
        return strip_tags(trim($value));
    }
}
