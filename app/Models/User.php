<?php

namespace App\Models;

use App\Models\Profile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable {
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [ 
		'name',
		'email',
		'username',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [ 
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [ 
		'email_verified_at' => 'datetime',
	];

	protected static function boot() {

		parent::boot();

		static::created( function ($user) {

			$slug = Str::of( $user->name )->slug();

			$profile = Profile::where( 'slug', $slug )->first();

			if ( $profile ) {
				$slug = $slug . '-' . uniqid();
			}

			$user->profile()->create( [ 
				'name' => $user->name,
				'slug' => $slug,

			] );

		} );

	}

	public function posts() {
		return $this->hasMany( Post::class)->orderBy( 'created_at', 'DESC' );
	}

	public function likes() {
		return $this->belongsToMany( Post::class, 'likes', 'user_id', 'post_id' );
	}

	public function liked() {
		return $this->belongsToMany( Post::class, 'likes', 'user_id', 'post_id' );
	}

	public function profile() {
		return $this->hasOne( Profile::class);
	}

	public function comments() {
		return $this->hasMany( Comment::class);
	}

}
