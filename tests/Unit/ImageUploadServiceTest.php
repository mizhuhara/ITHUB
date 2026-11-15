<?php

namespace Tests\Unit;

use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Tests\TestCase;

class ImageUploadServiceTest extends TestCase
{
    public function test_it_stores_and_calls_optimizer_for_uploaded_images(): void
    {
        Storage::fake('public');

        ImageOptimizer::shouldReceive('optimize')
            ->once()
            ->withArgs(fn (string $path): bool => str_ends_with($path, '.jpg'));

        $file = UploadedFile::fake()->image('banner.jpg', 800, 600);
        $storedPath = ImageUploadService::storeAndOptimize($file, 'banners');

        $this->assertNotEmpty($storedPath);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_it_compresses_large_jpeg_via_gd_fallback_when_cli_binaries_are_unavailable(): void
    {
        Storage::fake('public');

        $tempJpeg = tempnam(sys_get_temp_dir(), 'raw_');
        $this->assertNotFalse($tempJpeg);
        $rawPath = $tempJpeg.'.jpg';
        @unlink($tempJpeg);

        $canvas = imagecreatetruecolor(800, 600);
        $this->assertNotFalse($canvas);

        for ($y = 0; $y < 600; $y += 10) {
            for ($x = 0; $x < 800; $x += 10) {
                $color = imagecolorallocate($canvas, ($x * 3) % 255, ($y * 5) % 255, (($x + $y) * 7) % 255);
                imagefilledrectangle($canvas, $x, $y, $x + 9, $y + 9, $color);
            }
        }

        imagejpeg($canvas, $rawPath, 100);
        imagedestroy($canvas);

        $originalSize = filesize($rawPath);
        $this->assertGreaterThan(0, $originalSize);

        $uploadedFile = new UploadedFile($rawPath, 'photo.jpg', 'image/jpeg', null, true);
        $storedPath = ImageUploadService::storeAndOptimize($uploadedFile, 'banners');

        $storedFullPath = Storage::disk('public')->path($storedPath);
        $compressedSize = filesize($storedFullPath);

        @unlink($rawPath);

        $this->assertLessThan($originalSize, $compressedSize);
    }

    public function test_it_stores_non_image_files_without_calling_optimizer(): void
    {
        Storage::fake('public');

        ImageOptimizer::shouldReceive('optimize')->never();

        $file = UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf');
        $storedPath = ImageUploadService::storeAndOptimize($file, 'payment-proofs');

        $this->assertNotEmpty($storedPath);
        Storage::disk('public')->assertExists($storedPath);
    }
}
