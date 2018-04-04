<?php

namespace InviNBG\UploadImage\Templates;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Large implements FilterInterface
{
    // 图片宽度
    private $width = 480;

    public function applyFilter(Image $image)
    {
        $width = $this->width;
        $height = ceil($image->height() / ($image->width() / $width));
        return $image->fit($width, $height);
    }
}