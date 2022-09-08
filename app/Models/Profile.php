<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'description',
        'image',
        'links'
    ];
	
	public function profileImage(){
		
		// Show profile image - or default image
		$imagePath = ($this->image) ?  $this->image : 'profile/default.png';
		return '/storage/'.$imagePath;
	}
	
    public function user(){
        return $this->belongsTo(User::class);
    }

}
