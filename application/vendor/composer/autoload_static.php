<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita86063deaf671f15ede8ae84a0ec5650
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Skyeng\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Skyeng\\' => 
        array (
            0 => __DIR__ . '/..' . '/skyeng/php-lemmatizer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita86063deaf671f15ede8ae84a0ec5650::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita86063deaf671f15ede8ae84a0ec5650::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
