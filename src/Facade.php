<?php
/**
 * @date        2018/7/17
 * @author      invinbg <253618519@qq.com>
 * @copyright   Copyright (c)
 *
 */

namespace InviNBG\UploadImage;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    public static function getFacadeAccessor()
    {
        return 'upload';
    }
}