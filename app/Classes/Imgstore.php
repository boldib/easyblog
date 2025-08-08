<?php

namespace App\Classes;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class Imgstore {

	public static function setProfileImage( $image ) {
		if ( isset( $image ) ) {
			Image::make( $image )->encode( 'webp', 100 )->fit( 400, 400 )->save();
			$imgfilename = uniqid() . '.webp';
			Storage::disk( 'local' )->put( 'public/profiles/' . $imgfilename, fopen( $image, 'r+' ) );
			return $imgfilename;
		}
	}

	public static function setPostImage( $image ) {
		if ( isset( $image ) ) {
			Image::make( $image )->encode( 'webp', 100 )->fit( 400, 400 )->save();
			$imgfilename = uniqid() . '.webp';
			Storage::disk( 'local' )->put( 'public/images/' . $imgfilename, fopen( $image, 'r+' ) );
			return $imgfilename;
		}
	}

}
