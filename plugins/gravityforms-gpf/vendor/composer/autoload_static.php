<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit15ae3f2d144cb278a55d9472f9d3bfc0
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'l' => 
        array (
            'libphonenumber\\' => 15,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
        ),
        'G' => 
        array (
            'Greenpeacefrance\\Gravityforms\\' => 30,
            'Giggsey\\Locale\\' => 15,
        ),
        'B' => 
        array (
            'Brick\\PhoneNumber\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'libphonenumber\\' => 
        array (
            0 => __DIR__ . '/..' . '/giggsey/libphonenumber-for-php/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Greenpeacefrance\\Gravityforms\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Giggsey\\Locale\\' => 
        array (
            0 => __DIR__ . '/..' . '/giggsey/locale/src',
        ),
        'Brick\\PhoneNumber\\' => 
        array (
            0 => __DIR__ . '/..' . '/brick/phonenumber/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit15ae3f2d144cb278a55d9472f9d3bfc0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit15ae3f2d144cb278a55d9472f9d3bfc0::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit15ae3f2d144cb278a55d9472f9d3bfc0::$classMap;

        }, null, ClassLoader::class);
    }
}