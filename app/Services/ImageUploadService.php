<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Throwable;

class ImageUploadService
{
    /**
     * Image extensions eligible for optimization.
     *
     * @var array<int, string>
     */
    private const OPTIMIZABLE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    /**
     * JPEG and WebP compression quality for the PHP GD fallback.
     */
    private const GD_QUALITY = 82;

    /**
     * Store an uploaded file on the given disk and optimize it when it is an image.
     */
    public static function storeAndOptimize(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $path = $file->store($directory, $disk);

        if ($path === false) {
            return '';
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, self::OPTIMIZABLE_EXTENSIONS, true)) {
            return $path;
        }

        $fullPath = Storage::disk($disk)->path($path);

        if (! is_file($fullPath)) {
            return $path;
        }

        $originalSize = filesize($fullPath) ?: 0;

        try {
            ImageOptimizer::optimize($fullPath);
        } catch (Throwable $exception) {
            // Do not block user uploads when optional OS optimizer binaries are unavailable.
            Log::debug('Spatie image optimization skipped.', [
                'path' => $path,
                'error' => $exception->getMessage(),
            ]);
        }

        clearstatcache(true, $fullPath);
        $afterSpatieSize = filesize($fullPath) ?: 0;

        if ($originalSize > 0 && $afterSpatieSize >= $originalSize) {
            self::compressWithGd($fullPath, $extension, $afterSpatieSize);
        }

        return $path;
    }

    /**
     * Compress an image in-place using PHP GD when OS optimizer binaries did not reduce its size.
     */
    private static function compressWithGd(string $fullPath, string $extension, int $currentSize): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $image = match ($extension) {
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($fullPath) : false,
            'png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($fullPath) : false,
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : false,
            default => false,
        };

        if ($image === false) {
            return;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'imgopt_');

        if ($tempPath === false) {
            imagedestroy($image);

            return;
        }

        try {
            if ($extension === 'png' || $extension === 'webp') {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            $written = match ($extension) {
                'jpg', 'jpeg' => imagejpeg($image, $tempPath, self::GD_QUALITY),
                'png' => imagepng($image, $tempPath, 9),
                'webp' => function_exists('imagewebp') ? imagewebp($image, $tempPath, self::GD_QUALITY) : false,
                default => false,
            };

            if ($written && is_file($tempPath)) {
                $compressedSize = filesize($tempPath) ?: 0;

                if ($compressedSize > 0 && $compressedSize < $currentSize) {
                    @copy($tempPath, $fullPath);
                }
            }
        } finally {
            imagedestroy($image);

            if (is_file($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
}
