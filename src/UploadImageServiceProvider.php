<?php

namespace InviNBG\UploadImage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

class UploadImageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('imagecache.php'),
        ]);
    }
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/config.php', 'imagecache'
        );
    }
}