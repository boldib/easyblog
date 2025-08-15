<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileOwnership
{
    /**
     * Handle an incoming request to ensure the authenticated user owns the profile.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }

        // Get profile slug from route parameter
        $profileSlug = $request->route('profileslug') ?? $request->route('slug');
        
        if (!$profileSlug) {
            abort(400, 'Profile slug not found in route.');
        }

        // Find the profile
        $profile = Profile::where('slug', $profileSlug)->first();
        
        if (!$profile) {
            abort(404, 'Profile not found.');
        }

        // Check if the current user owns this profile or is admin
        $currentUserId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        if ($currentUserId != $profile->user_id && !$isAdmin) {
            abort(403, 'Unauthorized to access this profile.');
        }

        return $next($request);
    }
}
