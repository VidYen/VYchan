<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit04042c15ec5100a91dedccb235a39028
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Mdanter\\Ecc\\' => 12,
        ),
        'F' => 
        array (
            'FG\\' => 3,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Mdanter\\Ecc\\' => 
        array (
            0 => __DIR__ . '/..' . '/mdanter/ecc/src',
        ),
        'FG\\' => 
        array (
            0 => __DIR__ . '/..' . '/fgrosse/phpasn1/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit04042c15ec5100a91dedccb235a39028::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit04042c15ec5100a91dedccb235a39028::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}