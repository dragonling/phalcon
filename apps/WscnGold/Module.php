<?php

namespace WscnGold;

use Phalcon\Loader;
use Eva\EvaEngine\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'WscnGold' => __DIR__ . '/src/WscnGold',
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
            $dispatcher->setDefaultNamespace('WscnGold\Controllers');
			return $dispatcher;
		};

        View::registerComponent('post', 'Eva\EvaBlog\Components\Post');
    }

}
