<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0f8ac64e4573a39b593750bba0ec2a77
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0f8ac64e4573a39b593750bba0ec2a77::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0f8ac64e4573a39b593750bba0ec2a77::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
