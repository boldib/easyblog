<?php

namespace App\Http\Controllers;

use App\Interfaces\PostRepositoryInterface;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsible for handling post-related HTTP requests.
 *
 * This controller delegates business logic to the PostRepository to keep
 * controllers thin and adherent to the Single Responsibility Principle.
 */
class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    /**
     * Display the specified post.
     *
     * @param string $profileSlug The profile slug of the post owner
     * @param string $postSlug    The slug of the post to display
     *
     * @return ViewContract
     */
    public function show(string $profileSlug, string $postSlug): ViewContract
    {
        $post = $this->postRepository->show($profileSlug, $postSlug);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for creating a new post.
     *
     * @return ViewContract
     */
    public function create(): ViewContract
    {
        $user = $this->postRepository->create();

        return view('posts.create', compact('user'));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param Request $request The incoming HTTP request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $redirectTo = $this->postRepository->store($request);

        return redirect($redirectTo);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param int $postId The ID of the post to delete
     *
     * @return RedirectResponse
     */
    public function delete(int $postId): RedirectResponse
    {
        $deleted = $this->postRepository->delete($postId, (int) Auth::id());

        // On success redirect to the home page; repository aborts(403) on unauthorized.
        return redirect('/');
    }
}
