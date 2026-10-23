<?php

namespace App\Services;

class FrontendPhotoService
{
    public function watermark(string $contents): string
    {
        $image = @imagecreatefromstring($contents);
        abort_if($image === false, 415, 'Formato de imagen no compatible.');

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
        imagejpeg($image, null, 85);
        $result = ob_get_clean();
        imagedestroy($label);
        imagedestroy($image);

        return $result;
    }
}
