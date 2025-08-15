<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommentOwnership
{
    /**
     * Handle an incoming request to ensure the authenticated user owns the comment.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }

        // Get comment ID from route parameter
        $commentId = $request->route('id');
        
        if (!$commentId) {
            abort(400, 'Comment ID not found in route.');
        }

        // Find the comment
        $comment = Comment::find($commentId);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        // Check if the current user owns this comment or is admin
        $currentUserId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        if ($currentUserId != $comment->user_id && !$isAdmin) {
            abort(403, 'Unauthorized to access this comment.');
        }

        return $next($request);
    }
}
