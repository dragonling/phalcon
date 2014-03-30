<?php

namespace Eva\EvaFileSystem;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\EvaFileSystem' => __DIR__ . '/src/EvaFileSystem',
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
            'Eva\EvaFileSystem' => __DIR__ . '/src/EvaFileSystem',
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
		$di['dispatcher'] = function() {
			$dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace('Eva\EvaFileSystem\Controllers');
			return $dispatcher;
		};

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            return $view;
        };
    }

}
