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

class EvaEngine
{

    protected $appRoot;

    protected $moduleRoot;

    protected $di;

    protected $application;

    public function initErrorHandler()
    {
    
    }

    public function initDi()
    {
    
    }

    //Modult could return module root path
    public function initModule()
    {
    }

    public function initService()
    {
    
    }

    public function initConfig()
    {
    
    }

    public function initRouter()
    {
    
    }


    public function initCache()
    {
    }


    public function bootstrap()
    {
        $this->initService();
        $this->initDi();

        //Error Handler must run before router start
        $this->initErrorHandler();
        $this->initRouter();

    }

    public function __construct($appRoot, $moduleRoot = null)
    {
        
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
    echo "<pre>$e</pre>";
    //echo $e->getMessage();
} catch (PDOException $e) {
    var_dump($e);
    //echo $e->getMessage();
}
