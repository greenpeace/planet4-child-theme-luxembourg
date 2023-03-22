<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit983ad9fafaeef1c5706d10dd74fad2d7
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Greenpeacefrance\\Gravityforms\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Greenpeacefrance\\Gravityforms\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit983ad9fafaeef1c5706d10dd74fad2d7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit983ad9fafaeef1c5706d10dd74fad2d7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit983ad9fafaeef1c5706d10dd74fad2d7::$classMap;

        }, null, ClassLoader::class);
    }
}
