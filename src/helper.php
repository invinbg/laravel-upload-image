<?php

/**
 * 返回图片路径
 * @param string $image
 * @param string $template ['small','medium','large','original']
 * @return string
 */
if(! function_exists('image')) {
    function image($image = '', $template = 'small') : string
    {
        if(stripos($image, '://') !== false)
        {
            return $image;
        }
        else
        {
            if( \Storage::exists($image))
            {

                return route('imagecache',['template' => $template,'filename' => $image]);
            }
        }
        return '';

    }
}