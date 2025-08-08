<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Profile;
use App\Classes\Imgstore;
use App\Classes\Tagpost;
use App\Interfaces\PostRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostRepository implements PostRepositoryInterface {
	public function show( $profileSlug, $postSlug ) {
		$profile = Profile::where( 'slug', $profileSlug )->firstOrFail();
		return Post::where( 'user_id', $profile->user->id )
			->where( 'slug', $postSlug )
			->firstOrFail();
	}

	public function create() {
		return auth()->user();
	}

	public function store( Request $request ) {
		$data = request()->validate( [ 
			'title' => 'required',
			'content' => 'required',
			'image' => 'nullable|image|max:1024',
			'tags' => [ 'nullable', 'max:150' ],
		] );

		$post = Post::create( [ 
			'user_id' => Auth::id(),
			'title' => $data['title'],
			'content' => $data['content'],
			'image' => Imgstore::setPostImage( $request->file( 'image' ) ),
			'slug' => Str::of( $data['title'] )->slug(),
		] );

		Tagpost::sync( $data['tags'], $post );

		return "/" . $post->user->profile->slug . "/" . $post->slug;
	}

	public function delete( $postId, $authId ) {
		$post = Post::where( 'id', $postId )->firstOrFail();
		if ( $authId != $post->user->id )
			abort( 403 );
		$post->delete();
		return true;
	}

}