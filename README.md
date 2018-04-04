# laravel-upload-image

## Installation

You can install this package quickly and easily with Composer.

Require the package via Composer:

    $ composer require invinbg/laravel-upload-image

Finally publish the config file:

    $ php artisan vendor:publish --provider="InviNBG\UploadImage\UploadImageServiceProvider"

### Laravel Integration

The Image Cache class supports Laravel integration. Best practice to use the library in Laravel is to add the ServiceProvider and Facade of the Intervention Image Class.

Open your Laravel config file `config/app.php` and add the following lines.

In the `$providers` array add the service providers for this package.

    'providers' => array(

        [...]

        'Intervention\Image\ImageServiceProvider'
    ),


Add the facade of this package to the `$aliases` array.

    'aliases' => array(

        [...]

        'Image' => 'Intervention\Image\Facades\Image'
    ),