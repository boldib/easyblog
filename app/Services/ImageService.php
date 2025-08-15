<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

abstract class ImageProcessor
{
    /**
     * Template method for image processing.
     */
    public function processImage(UploadedFile|string|null $image): ?string
    {
        if ($image === null) {
            return null;
        }

        try {
            $this->logUploadAttempt($image);
            
            $processedImage = $this->createImage($image);
            $filename = $this->generateFilename();
            $path = $this->getStoragePath($filename);
            
            $this->ensureDirectoryExists();
            
            // Write the file and verify it was written
            $success = Storage::disk('local')->put($path, (string) $processedImage);
            
            if ($success) {
                // Verify the file actually exists after upload
                if (Storage::disk('local')->exists($path)) {
                    $this->logSuccess($filename, $path);
                    return $filename;
                } else {
                    $this->logError('Image file not found after upload', ['path' => $path]);
                    return null;
                }
            } else {
                $this->logError('Failed to write image to storage', ['path' => $path]);
                return null;
            }
            
        } catch (\Exception $e) {
            $this->logError('Image upload failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Create and process the image.
     */
    protected function createImage(UploadedFile|string $image)
    {
        $img = Image::make($image)
            ->fit(400, 400)
            ->encode('webp', 100);
        
        return $img;
    }

    /**
     * Generate unique filename.
     */
    protected function generateFilename(): string
    {
        return (string) Str::uuid() . '.webp';
    }

    /**
     * Get the storage path for the image.
     */
    abstract protected function getStoragePath(string $filename): string;

    /**
     * Ensure the storage directory exists.
     */
    abstract protected function ensureDirectoryExists(): void;

    /**
     * Log successful upload.
     */
    abstract protected function logSuccess(string $filename, string $path): void;

    /**
     * Log upload attempt.
     */
    protected function logUploadAttempt(UploadedFile|string $image): void
    {
        Log::info('Image upload attempt', [
            'image_provided' => $image !== null,
            'image_type' => $image ? get_class($image) : 'null',
            'original_name' => method_exists($image, 'getClientOriginalName') ? $image->getClientOriginalName() : 'N/A',
            'size' => method_exists($image, 'getSize') ? $image->getSize() : 'N/A',
            'mime_type' => method_exists($image, 'getMimeType') ? $image->getMimeType() : 'N/A'
        ]);
    }

    /**
     * Log error.
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }
}

class ProfileImageProcessor extends ImageProcessor
{
    protected function getStoragePath(string $filename): string
    {
        return 'public/profiles/' . $filename;
    }

    protected function ensureDirectoryExists(): void
    {
        $path = 'public/profiles';
        Storage::disk('local')->makeDirectory($path);
        
        // Fix permissions for Docker environments
        $fullPath = storage_path('app/' . $path);
        if (is_dir($fullPath)) {
            chmod($fullPath, 0755);
        }
    }

    protected function logSuccess(string $filename, string $path): void
    {
        Log::info('Profile image uploaded successfully', [
            'filename' => $filename,
            'path' => $path,
            'size' => Storage::disk('local')->size($path)
        ]);
    }
}

class PostImageProcessor extends ImageProcessor
{
    protected function getStoragePath(string $filename): string
    {
        return 'public/images/' . $filename;
    }

    protected function ensureDirectoryExists(): void
    {
        $path = 'public/images';
        Storage::disk('local')->makeDirectory($path);
        
        // Fix permissions for Docker environments
        $fullPath = storage_path('app/' . $path);
        if (is_dir($fullPath)) {
            chmod($fullPath, 0755);
        }
    }

    protected function logSuccess(string $filename, string $path): void
    {
        Log::info('Post image uploaded successfully', [
            'filename' => $filename,
            'path' => $path
        ]);
    }
}

class ImageService
{
    public static function setProfileImage(UploadedFile|string|null $image): ?string
    {
        $processor = new ProfileImageProcessor();
        return $processor->processImage($image);
    }

    public static function setPostImage(UploadedFile|string|null $image): ?string
    {
        $processor = new PostImageProcessor();
        return $processor->processImage($image);
    }
}
