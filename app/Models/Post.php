<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'slug', 'content', 'image', 'status'
    ];

    /**
     * The author of the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Users who liked this post (many-to-many via likes pivot).
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'user_id', 'post_id');
    }

    /**
     * Like records associated with this post.
     */
    public function liked(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Tags associated with this post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    /**
     * Comments posted under this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Resolve the public URL to the post image.
     */
    public function image(): string
    {
        $imageSource = ($this->image) ? $this->image : 'default.webp';

        if (str_contains($imageSource, 'picsum')) {
            return $imageSource;
        }

        return '/storage/images/' . $imageSource;
    }
}
