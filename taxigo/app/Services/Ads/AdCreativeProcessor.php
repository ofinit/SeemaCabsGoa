<?php

namespace App\Services\Ads;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use RuntimeException;

/**
 * Creative pipeline (plan §8): the browser sends the original image plus the
 * crop box chosen in Cropper.js; the server validates the file, keeps the
 * original privately, auto-rotates, strips metadata (EXIF / GPS), applies the
 * crop, resizes to the placement's master size and encodes WebP, stepping the
 * quality down until it fits the size budget.
 */
class AdCreativeProcessor
{
    public const MAX_BYTES = 10 * 1024 * 1024;
    public const MAX_SIDE = 8000;
    public const TARGET_BYTES = 150 * 1024;
    public const MIN_QUALITY = 65;

    /**
     * @param  array{x: float, y: float, width: float, height: float, rotate?: int}  $crop
     * @return array{original_path: string, file: string, width: int, height: int, bytes: int, sha1: string, crop: array}
     */
    public function process(UploadedFile $upload, array $crop, int $width, int $height): array
    {
        if ($upload->getSize() > self::MAX_BYTES) {
            throw new RuntimeException('The image is larger than 10 MB.');
        }

        // Check the real type and pixel size before decoding (decompression-bomb guard).
        $info = @getimagesize($upload->getRealPath());
        $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
        if (!$info || !isset($types[$info[2]])) {
            throw new RuntimeException('Upload a JPEG, PNG or WebP image.');
        }
        if ($info[0] > self::MAX_SIDE || $info[1] > self::MAX_SIDE) {
            throw new RuntimeException('The image is larger than 8000 × 8000 pixels.');
        }

        $sha1 = sha1_file($upload->getRealPath());
        $originalPath = 'ads/originals/' . $sha1 . '.' . $types[$info[2]];
        if (!Storage::disk('local')->exists($originalPath)) {
            Storage::disk('local')->putFileAs('ads/originals', $upload, $sha1 . '.' . $types[$info[2]]);
        }

        return $this->render($upload->getRealPath(), $originalPath, $sha1, $crop, $width, $height);
    }

    /** Re-crop a previously uploaded original (kept privately) without uploading again. */
    public function recrop(string $originalPath, array $crop, int $width, int $height): array
    {
        if (!str_starts_with($originalPath, 'ads/originals/') || !Storage::disk('local')->exists($originalPath)) {
            throw new RuntimeException('The original image is no longer available. Please upload it again.');
        }
        $realPath = Storage::disk('local')->path($originalPath);

        return $this->render($realPath, $originalPath, sha1_file($realPath), $crop, $width, $height);
    }

    private function render(string $realPath, string $originalPath, string $sha1, array $crop, int $width, int $height): array
    {
        $image = ImageManager::gd(autoOrientation: true, strip: true)->read($realPath);

        $rotate = (int) ($crop['rotate'] ?? 0);
        $rotate = ((($rotate % 360) + 360) % 360);
        if (!in_array($rotate, [0, 90, 180, 270], true)) {
            throw new RuntimeException('Rotate the image in 90° steps.');
        }
        if ($rotate) {
            // Cropper.js rotates clockwise; Intervention rotates counter-clockwise.
            $image->rotate(-$rotate);
        }

        $x = max(0, (int) round($crop['x'] ?? 0));
        $y = max(0, (int) round($crop['y'] ?? 0));
        $w = min((int) round($crop['width'] ?? 0), $image->width() - $x);
        $h = min((int) round($crop['height'] ?? 0), $image->height() - $y);

        // At least half the master size, so the ad isn't blurry when upscaled.
        if ($w < $width / 2 || $h < $height / 2) {
            throw new RuntimeException("The selected area is too small. Use an image of at least {$width} × {$height} px for best results (minimum " . intdiv($width, 2) . ' × ' . intdiv($height, 2) . ' px).');
        }
        $ratio = $width / $height;
        if (abs(($w / $h) - $ratio) > 0.03 * $ratio) {
            throw new RuntimeException('The crop does not match the placement shape. Please crop again.');
        }

        $image->crop($w, $h, $x, $y)->resize($width, $height);

        $quality = 80;
        $encoded = $image->toWebp(quality: $quality);
        while ($encoded->size() > self::TARGET_BYTES && $quality > self::MIN_QUALITY) {
            $quality = max(self::MIN_QUALITY, $quality - 5);
            $encoded = $image->toWebp(quality: $quality);
        }

        $bytes = $encoded->toString();
        $file = 'ad-' . substr(sha1($bytes), 0, 20) . '-' . $width . 'x' . $height . '.webp';
        Storage::disk('public')->put('banner/' . $file, $bytes);

        return [
            'original_path' => $originalPath,
            'file' => $file,
            'width' => $width,
            'height' => $height,
            'bytes' => strlen($bytes),
            'sha1' => $sha1,
            'crop' => ['x' => $x, 'y' => $y, 'width' => $w, 'height' => $h, 'rotate' => $rotate],
        ];
    }
}
