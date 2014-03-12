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
        /*
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'Eva\EvaCore' => __DIR__ . '/src/EvaCore',
        ));
        $loader->register();
        */
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {

        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";

		$di['dispatcher'] = function() {
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace('Eva\EvaCore\Controllers');
			return $dispatcher;
		};

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            $view->setLayoutsDir(__DIR__ . '/layouts/');
            return $view;
        };
    }
}
