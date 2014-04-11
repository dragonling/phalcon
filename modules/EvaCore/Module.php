<?php

namespace Eva\EvaCore;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{

    public static $moduleName = 'EvaCore';

    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\EvaCore' => __DIR__ . '/src/EvaCore',
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
		$di['dispatcher'] = function() {
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace('Eva\EvaCore\Controllers');
			return $dispatcher;
		};
    }
}
