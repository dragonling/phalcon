<?php
error_reporting(E_ALL);
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $loader = include __DIR__ . '/vendor/autoload.php';
} else {
    throw new RuntimeException('Unable to find loader. Run `php composer.phar install` first.');
}

$loader->addPsr4('Eva\\EvaEngine\\', __DIR__ . '/modules/EvaEngine/src/EvaEngine/');

function p($r)
{
    if (function_exists('xdebug_var_dump')) {
        echo '<pre>';
        xdebug_var_dump($r);
        echo '</pre>';
        //(new \Phalcon\Debug\Dump())->dump($r, true);
    } else {
        echo '<pre>';
        var_dump($r);
        echo '</pre>';
    }
}
