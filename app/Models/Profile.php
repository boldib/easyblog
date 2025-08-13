<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Resolve the public URL to the profile image.
     */
    public function image(): string
    {
        $imageSource = ($this->image) ? $this->image : 'default.webp';

        if (str_contains($imageSource, 'picsum')) {
            return $imageSource;
        }

        return '/storage/profiles/' . $imageSource;
    }

    /**
     * The user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
