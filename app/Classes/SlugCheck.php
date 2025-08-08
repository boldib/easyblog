<?php

namespace App\Classes;
use Auth;
use App\Models\Profile;
use Illuminate\Support\Str;

class SlugCheck {

	public $word;

	public function __construct( $word ) {
		$this->word = $word;
	}

	public function isForbidden() {
		$array = array( "admin", "search", "terms-of-service", "tos", "tags" );
		if ( in_array( Str::of( $this->word )->slug(), $array ) ) {
			return true;
		}
	}

	public function isUsed() {
		$urlExists = Profile::where( 'slug', Str::of( $this->word )->slug() )->first();
		if ( isset( $urlExists->slug ) ) {
			if ( $urlExists->user->id != Auth::user()->id ) {
				return true;
			}
		}
	}


}

?>