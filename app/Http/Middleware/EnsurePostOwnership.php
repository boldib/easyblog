<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePostOwnership
{
    /**
     * Handle an incoming request to ensure the authenticated user owns the post.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }

        // Get post ID from route parameter (for edit/update/delete routes)
        $postId = $request->route('id');
        
        if (!$postId) {
            abort(400, 'Post ID not found in route.');
        }

        // Find the post
        $post = Post::find($postId);
        
        if (!$post) {
            abort(404, 'Post not found.');
        }

        // Check if the current user owns this post or is admin
        $currentUserId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        if ($currentUserId != $post->user_id && !$isAdmin) {
            abort(403, 'Unauthorized to access this post.');
        }

        return $next($request);
    }
}
