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
	
	public function image(){

        $imageSource = ($this->image) ? $this->image : 'default.webp';

        if(str_contains($imageSource, 'picsum')) {
            return $imageSource;
        }

		return '/storage/profiles/'.$imageSource;
	}
	
    public function user(){
        return $this->belongsTo(User::class);
    }

}
