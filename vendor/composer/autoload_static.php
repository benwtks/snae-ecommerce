<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0300001f5637687abc764e103e1c91fe
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0300001f5637687abc764e103e1c91fe::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0300001f5637687abc764e103e1c91fe::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
