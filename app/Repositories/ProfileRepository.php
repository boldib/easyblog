<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Classes\SlugCheck;
use App\Classes\Imgstore;
use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Support\Str;

class ProfileRepository implements ProfileRepositoryInterface {
	public function getProfile( $profileSlug ) {
		$profile = Profile::where( 'slug', $profileSlug )->firstorFail();
		return $profile;
	}

	public function getProfileById( $id ) {
		$profile = Profile::where( 'id', $id )->firstorFail();
		return $profile;
	}

	public function getProfilePosts( $profile ) {
		$posts = Post::where( 'user_id', $profile->user->id )
			->orderByDesc( 'id' )
			->paginate( 10 );
		return $posts;
	}

	public function update( $request, $profileSlug, $auth ) {
		$profile = Profile::where( 'slug', $profileSlug )->firstorFail();
		if ( $profile->user->id != $auth->id || $auth->role != 'admin' )
			abort( 403 );

		$data = request()->validate( [ 
			'name' => [ 'required', 'max:32' ],
			'description' => 'nullable|max:10000',
			'slug' => [ 'required', 'max:32' ],
			'image' => 'nullable|image|max:1024',
		] );

		$slugCheck = new SlugCheck( $data['slug'] );
		if ( $slugCheck->isForbidden() || $slugCheck->isUsed() )
			return redirect()->back()->withErrors( [ 'slug' => 'This url is already used' ] );

		$profile->user->name = $data['name'];
		$profile->slug = Str::of( $data['slug'] )->slug();
		$profile->image = Imgstore::setProfileImage( $request->file( 'image' ) );
		$profile->description = ( isset( $data['description'] ) ) ? $data['description'] : NULL;
		$profile->update();
		$profile->user->update();
		return $profile;
	}

	public function delete( $id, $auth ) {
		$profile = Profile::where( 'id', $id )->firstOrFail();
		$user = User::where( 'id', $profile->user->id )->firstOrFail();
		if ( $profile->user->id != $auth->id || $auth->role != 'admin' )
			abort( 403 );

		$user->posts()->delete();
		$user->likes()->delete();
		$user->comments()->delete();
		$user->profile()->delete();
		$user->delete();
	}

}