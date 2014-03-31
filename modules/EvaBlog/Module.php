<?php

namespace Eva\EvaBlog;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\EvaBlog' => __DIR__ . '/src/EvaBlog',
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
            'Eva\EvaBlog' => __DIR__ . '/src/EvaBlog',
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
            $dispatcher->setDefaultNamespace('Eva\EvaBlog\Controllers');
			return $dispatcher;
		};

        /**
         * Setting up the view component
         */
        $di['view'] = function () use ($di) {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            return $view;
        };
    }

}
