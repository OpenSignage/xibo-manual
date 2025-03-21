<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9abcd5f18e6c68916efbf681369acac2
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Google\\Cloud\\DiscoveryEngine\\' => 29,
            'GPBMetadata\\Google\\Cloud\\Discoveryengine\\' => 41,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Google\\Cloud\\DiscoveryEngine\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'GPBMetadata\\Google\\Cloud\\Discoveryengine\\' => 
        array (
            0 => __DIR__ . '/../..' . '/metadata',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9abcd5f18e6c68916efbf681369acac2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9abcd5f18e6c68916efbf681369acac2::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
