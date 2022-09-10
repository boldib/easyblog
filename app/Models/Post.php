<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id', 'title', 'slug', 'content', 'image', 'status'
    ];
	
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function likes(){
		return $this->belongsToMany(User::class, 'likes', 'user_id', 'post_id');
    }

    public function liked(){
		return $this->hasMany(Like::class);
    }
    
    public function tags()
    {   
      return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function comments()
    {
      return $this->hasMany(Comment::class);
    }

    public function image(){
      
      $imageSource = ($this->image) ? $this->image : 'default.webp';

      if(str_contains($imageSource, 'picsum')) {
        return $imageSource;
      }
    
  		return '/storage/images/'.$imageSource;
    }
}
