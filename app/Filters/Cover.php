<?php

namespace App\Filters;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Cover implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        if ($image->width() >= 300) {
            $image->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $image->encode('jpg', config('settings.jpeg_quality'));
    }
}
