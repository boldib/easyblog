<?php

namespace App\Classes;

use App\Models\Tag;
use App\Models\Post;

use Illuminate\Support\Str;

class Tagpost
{
    public static function sync($tags, Post $post)
    {
        if(isset($tags) && $post)
        { 
            $tagNames = explode(',',$tags);
		    $tagIds = [];
		    $tagcount = 0;

		    foreach($tagNames as $tagName){
				$tagcount++;
                $tag = Tag::firstOrCreate([
					'title'=> strtolower($tagName),
					'slug' => Str::of($tagName)->slug(),
				]);

				if($tag) $tagIds[] = $tag->id;
				if($tagcount == 6) break;
			}
            $post->tags()->sync($tagIds);
		}
    }
}
