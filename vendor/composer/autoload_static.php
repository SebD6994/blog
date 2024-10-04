<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb8aa1a960b0510738148e940ddd2d96a
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
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb8aa1a960b0510738148e940ddd2d96a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb8aa1a960b0510738148e940ddd2d96a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb8aa1a960b0510738148e940ddd2d96a::$classMap;

        }, null, ClassLoader::class);
    }
}
