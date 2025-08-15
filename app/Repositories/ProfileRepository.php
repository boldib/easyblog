<?php

namespace App\Repositories;

use App\Classes\Imgstore;
use App\Classes\SlugCheck;
use App\Interfaces\ProfileRepositoryInterface;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProfileRepository implements ProfileRepositoryInterface {
	/**
	 * Get a profile by its slug.
	 */
	public function getProfile( string $profileSlug ): Profile {
		return Profile::where( 'slug', $profileSlug )->firstOrFail();
	}

	/**
	 * Get a profile by its ID.
	 */
	public function getProfileById( int $profileId ): Profile {
		return Profile::where( 'id', $profileId )->firstOrFail();
	}

	/**
	 * Get paginated posts for a profile.
	 */
	public function getProfilePosts( Profile $profile ): LengthAwarePaginator {
		return Post::where( 'user_id', $profile->user->id )
			->orderByDesc( 'created_at' )
			->paginate( 10 );
	}

	/**
	 * Update a profile.
	 */
	public function update( Request $request, string $profileSlug, User $auth ): Profile {
		$profile = Profile::where( 'slug', $profileSlug )->firstorFail();
		if ( $profile->user->id != $auth->id && $auth->role != 'admin' ) {
			abort( 403 );
		}

		$data = request()->validate( [ 
			'name' => [ 'required', 'max:32' ],
			'description' => 'nullable|max:10000',
			'slug' => [ 'required', 'max:32' ],
			'image' => 'nullable|image|max:1024',
		] );

		$slugCheck = new SlugCheck( $data['slug'] );
		if ( $slugCheck->isForbidden() || $slugCheck->isUsed() ) {
			throw new \InvalidArgumentException( 'This URL is already used' );
		}

		$profile->user->name = $data['name'];
		$profile->slug = Str::of( $data['slug'] )->slug();

		// Only update image if a new one was uploaded
		$newImage = Imgstore::setProfileImage( $request->file( 'image' ) );
		if ( $newImage !== null ) {
			$profile->image = $newImage;
		}

		$profile->description = ( isset( $data['description'] ) ) ? $data['description'] : null;
		$profile->update();
		$profile->user->update();
		return $profile;
	}

	/**
	 * Delete a profile.
	 */
	public function delete( int $id, User $auth ): bool {
		$profile = Profile::where( 'id', $id )->firstOrFail();
		$user = User::where( 'id', $profile->user->id )->firstOrFail();
		if ( $profile->user->id != $auth->id && $auth->role != 'admin' ) {
			abort( 403 );
		}

		$user->posts()->delete();
		$user->likes()->delete();
		$user->comments()->delete();
		$user->profile()->delete();
		$user->delete();

		return true;
	}
}
