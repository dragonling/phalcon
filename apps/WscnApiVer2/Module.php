<?php

namespace WscnApiVer2;

use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Eva\EvaEngine\Error\ErrorHandler;

class Module implements ModuleDefinitionInterface
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'WscnApiVer2' => __DIR__ . '/src/WscnApiVer2',
        );
    }

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        $di['dispatcher'] = function () {
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace('WscnApiVer2\Controllers');

            return $dispatcher;
        };

        ErrorHandler::setErrorController('JsonError');
    }

}
