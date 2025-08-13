<?php

namespace App\Classes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Centralized image storage utilities.
 *
 * Converts uploaded images to optimized WebP and stores them
 * under the appropriate public disk directory.
 */
class Imgstore
{
    /**
     * Process and store a profile image as WebP 400x400.
     *
     * @param UploadedFile|string|null $image The uploaded file or local path
     * @return string|null The stored filename (e.g. uuid.webp) or null when no image given
     */
    public static function setProfileImage(UploadedFile|string|null $image): ?string
    {
        \Log::info('Profile image upload attempt', [
            'image_provided' => $image !== null,
            'image_type' => $image ? get_class($image) : 'null'
        ]);
        
        if ($image === null) {
            \Log::info('No image provided for profile upload');
            return null;
        }

        try {
            \Log::info('Processing profile image', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime_type' => $image->getMimeType()
            ]);
            
            $img = Image::make($image)
                ->fit(400, 400)
                ->encode('webp', 100);

            $filename = (string) Str::uuid() . '.webp';
            $path = 'public/profiles/' . $filename;
            
            // Ensure directory exists
            Storage::disk('local')->makeDirectory('public/profiles');
            
            // Write the file and verify it was written
            $success = Storage::disk('local')->put($path, (string) $img);
            
            if ($success) {
                // Verify the file actually exists
                if (Storage::disk('local')->exists($path)) {
                    \Log::info('Profile image uploaded successfully', [
                        'filename' => $filename,
                        'path' => $path,
                        'size' => Storage::disk('local')->size($path)
                    ]);
                    return $filename;
                } else {
                    \Log::error('Profile image file not found after upload', ['path' => $path]);
                    return null;
                }
            } else {
                \Log::error('Failed to write profile image to storage', ['path' => $path]);
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('Profile image upload failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Process and store a post image as WebP 400x400.
     *
     * @param UploadedFile|string|null $image The uploaded file or local path
     * @return string|null The stored filename (e.g. uuid.webp) or null when no image given
     */
    public static function setPostImage(UploadedFile|string|null $image): ?string
    {
        if ($image === null) {
            return null;
        }

        try {
            $img = Image::make($image)
                ->fit(400, 400)
                ->encode('webp', 100);

            $filename = (string) Str::uuid() . '.webp';
            
            // Ensure directory exists
            Storage::disk('local')->makeDirectory('public/images');
            
            Storage::disk('local')->put('public/images/' . $filename, (string) $img);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('Post image upload failed: ' . $e->getMessage());
            return null;
        }
    }
}
