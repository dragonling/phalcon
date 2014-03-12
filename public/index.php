<?php

use Phalcon\Mvc\Application;

error_reporting(E_ALL);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $loader = include __DIR__ . '/../vendor/autoload.php';
} else {
    throw new RuntimeException('Unable to find loader. Run `php composer.phar install` first.');
}

function p($r)
{
    if(class_exists('\Phalcon\Debug\Dump')) {
        xdebug_var_dump($r);
        //(new \Phalcon\Debug\Dump())->dump($r, true);
    } else {
        var_dump($r);
    }
}

try {

    /**
     * Include services
     */
    require __DIR__ . '/../config/services.php';

    /**
     * Handle the request
     */
    $application = new Application();

    /**
     * Assign the DI
     */
    $application->setDI($di);

    /**
     * Include modules
     */
    require __DIR__ . '/../config/modules.php';

    echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}
