<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\PostRepositoryInterface;

class PostsController extends Controller
{

    private PostRepositoryInterface $postRepository;
    
    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function show($profileSlug, $postSlug){
        $post = $this->postRepository->show($profileSlug, $postSlug);
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        $user = $this->postRepository->create();
        return view('posts.create', compact('user'));
    }

    public function store(Request $request){
        $post = $this->postRepository->store($request);
        return redirect($post);

    }

    public function delete($postId){
        $post = $this->postRepository->delete($postId, Auth::id());
        if($post) return redirect('/');        
    }
    
}
