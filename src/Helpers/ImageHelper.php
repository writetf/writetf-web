<?php

namespace App\Helpers;

class ImageHelper
{
    public static function getSize($absolutePath)
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);
        $content = file_get_contents($absolutePath, false, $context);
        $im = imagecreatefromstring($content);
        $width = imagesx($im);
        $height = imagesy($im);
        return [
            'width' => $width,
            'height' => $height,
        ];
    }
}
