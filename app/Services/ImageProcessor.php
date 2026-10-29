<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageProcessor
{
    public function process(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $sizeBytes = (int) $file->getSize();
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $imageInfo = @getimagesize($path);
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (! in_array($mime, $allowed, true) || ! $imageInfo || ! in_array($imageInfo['mime'] ?? null, $allowed, true)) {
            throw ValidationException::withMessages(['photos' => 'Foto harus berupa JPEG, PNG, atau WebP yang valid.']);
        }

        try {
            // Re-encode membuang metadata EXIF dan membatasi sisi panjang untuk efisiensi penyimpanan.
            $encoded = (new ImageManager(new Driver))->read($path)->scaleDown(width: 1280, height: 1280)->toJpeg(quality: 82)->toString();
        } catch (\Throwable) {
            throw ValidationException::withMessages(['photos' => 'Foto tidak dapat diproses. Silakan gunakan gambar lain.']);
        }

        return ['mime' => 'image/jpeg', 'data_base64' => base64_encode($encoded), 'size_bytes' => $sizeBytes];
    }
}
