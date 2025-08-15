<?php

namespace App\Repositories;

use App\Classes\SlugCheck;
use App\Classes\ValidationRuleFactory;
use App\Interfaces\ProfileRepositoryInterface;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Services\ImageService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
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
		AuthorizationService::canModifyProfile($profile, $auth);

		$data = request()->validate(ValidationRuleFactory::getProfileRules());

		$slugCheck = new SlugCheck( $data['slug'] );
		if ( $slugCheck->isForbidden() || $slugCheck->isUsed() ) {
			throw \Illuminate\Validation\ValidationException::withMessages([
				'slug' => 'This URL is already used or forbidden.'
			]);
		}

		$profile->user->name = sanitize_required($data['name']);
		$profile->slug = Str::of( $data['slug'] )->slug();

		// Only update image if a new one was uploaded
		$newImage = ImageService::setProfileImage( $request->file( 'image' ) );
		if ( $newImage !== null ) {
			$profile->image = $newImage;
		}

		$profile->description = sanitize($data['description'] ?? null);
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
		AuthorizationService::canModifyProfile($profile, $auth);

		$user->posts()->delete();
		$user->likes()->delete();
		$user->comments()->delete();
		$user->profile()->delete();
		$user->delete();

		return true;
	}
}
