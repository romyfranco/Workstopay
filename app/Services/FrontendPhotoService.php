<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FrontendPhotoService
{
    public function cached(string $path, bool $thumbnail = false): string
    {
        $source = Storage::disk('public');
        $cache = Storage::disk('local');
        $key = hash('sha256', implode('|', ['v2', $path, $source->lastModified($path), $source->size($path), $thumbnail ? 'thumb' : 'full']));
        $target = 'frontend-photos/'.$key.'.jpg';

        if (! $cache->exists($target)) {
            $bytes = $this->watermark($source->get($path), $thumbnail ? 240 : null);
            $temporary = $target.'.'.bin2hex(random_bytes(8)).'.tmp';
            $cache->put($temporary, $bytes);
            $cache->move($temporary, $target);
        }

        return $cache->get($target);
    }

    public function watermark(string $contents, ?int $maxDimension = null): string
    {
        $image = @imagecreatefromstring($contents);
        abort_if($image === false, 415, 'Formato de imagen no compatible.');

        if ($maxDimension && max(imagesx($image), imagesy($image)) > $maxDimension) {
            $ratio = $maxDimension / max(imagesx($image), imagesy($image));
            $resized = imagescale($image, max(1, (int) round(imagesx($image) * $ratio)), max(1, (int) round(imagesy($image) * $ratio)));
            imagedestroy($image);
            $image = $resized;
        }

        // Draw the label separately so its size follows the photo dimensions.
        $label = imagecreatetruecolor(350, 34);
        imagealphablending($label, false);
        imagesavealpha($label, true);
        imagefill($label, 0, 0, imagecolorallocatealpha($label, 0, 0, 0, 48));
        imagealphablending($label, true);
        $white = imagecolorallocate($label, 255, 255, 255);
        $red = imagecolorallocate($label, 255, 82, 109);
        imagestring($label, 5, 12, 9, 'hecho con', $white);
        imagefilledellipse($label, 106, 14, 12, 12, $red);
        imagefilledellipse($label, 116, 14, 12, 12, $red);
        imagefilledpolygon($label, [100, 15, 122, 15, 111, 27], $red);
        imagestring($label, 5, 132, 9, 'por Igmar Franco', $white);

        $width = imagesx($image);
        $height = imagesy($image);
        $labelWidth = min(700, (int) ($width * 0.9), (int) ($height * 0.25 * 350 / 34));
        $labelWidth = max(1, $labelWidth);
        $labelHeight = max(1, (int) ($labelWidth * 34 / 350));
        $margin = max(1, (int) (min($width, $height) * 0.025));
        imagealphablending($image, true);
        imagecopyresampled($image, $label, (int) (($width - $labelWidth) / 2), max(0, $height - $labelHeight - $margin), 0, 0, $labelWidth, $labelHeight, 350, 34);

        ob_start();
        imagejpeg($image, null, $maxDimension ? 75 : 85);
        $result = ob_get_clean();
        imagedestroy($label);
        imagedestroy($image);

        return $result;
    }
}
