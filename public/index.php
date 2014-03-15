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
    if(function_exists('xdebug_var_dump')) {
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

} catch (Exception $e) {
    echo "<pre>$e</pre>";
    //echo $e->getMessage();
} catch (PDOException $e) {
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    //echo $e->getMessage();
}
