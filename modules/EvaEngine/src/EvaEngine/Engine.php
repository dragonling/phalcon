<?php

namespace Eva\EvaEngine;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Config;
use Eva\EvaEngine\Mvc\View;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\Dispatcher;

use Eva\EvaEngine\ModuleManager;

class Engine
{
    protected $appRoot;

    protected $modulesPath;

    protected $di;

    protected $application;

    protected $configPath;

    public function setConfigPath($path)
    {
        $this->configPath = $path;

        return $this;
    }

    public function getConfigPath()
    {
        if ($this->configPath) {
            return $this->configPath;
        }

        return $this->configPath = $this->appRoot . '/config';
    }

    public function initErrorHandler(Error\ErrorHandlerInterface $errorHandler)
    {
        if($this->getDI()->get('config')->debug) {
            return $this;
        }

        $errorClass = get_class($errorHandler);
        set_error_handler("$errorClass::errorHandler");
        set_exception_handler("$errorClass::exceptionHandler");
        register_shutdown_function("$errorClass::shutdownHandler");
        return $this;
    }

    public function getApplication()
    {
        if ($this->application) {
            return $this->application;
        }

        return $this->application = new Application();
    }

    public function getDI()
    {
        if ($this->di) {
            return $this->di;
        }

        $di = new FactoryDefault();
        $self = $this;

        $di->set('app', function () use ($self) {
            return $self->getApplication();
        });

        //call loadmodules will overwrite this
        $di->set('moduleManager', function () {
            return new ModuleManager();
        });

        $di->set('config', function () use ($di, $self) {
            $config = new Config();

            //merge all loaded module configs
            $modules = $di->get('moduleManager');
            if ($modules && $modulesArray = $modules->getModules()) {
                foreach ($modulesArray as $moduleName => $module) {
                    $moduleConfig = $modules->getModuleConfig($moduleName);
                    if ($moduleConfig instanceof Config) {
                        $config->merge($moduleConfig);
                    } else {
                        $config->merge(new Config($moduleConfig));
                    }
                }
            }

            //merge config default
            $config->merge(new Config(include $self->getConfigPath() . "/config.default.php"));

            //merge config local
            if (false === file_exists($self->getConfigPath() . "/config.local.php")) {
                return $config;
            }
            $config->merge(new Config(include $self->getConfigPath() . "/config.local.php"));

            return $config;
        });

        $di->set('router', function () use ($di) {
            $moduleManager = $di->get('moduleManager');

            $config = new \Phalcon\Config();
            if ($moduleManager && $modulesArray = $moduleManager->getModules()) {
                foreach ($modulesArray as $moduleName => $module) {
                    $config->merge(new \Phalcon\Config($moduleManager->getModuleRoutesBackend($moduleName)));
                    $config->merge(new \Phalcon\Config($moduleManager->getModuleRoutesFrontend($moduleName)));
                }
            }

            $router = new Router();
            //$router->clear();
            //$router->removeExtraSlashes(true);
            $config = $config->toArray();
            foreach ($config as $url => $route) {
                if (count($route) !== count($route, COUNT_RECURSIVE)) {
                    if (isset($route['pattern']) && isset($route['paths'])) {
                        $method = isset($route['httpMethods']) ? $route['httpMethods'] : null;
                        $router->add($route['pattern'], $route['paths'], $method);
                    } else {
                        throw new Exception\InvalidArgumentException(sprintf('No route pattern and paths found by route %s', $url));
                    }
                } else {
                    $router->add($url, $route);
                }
            }

            return $router;
        });

        $di->set('url', function () use ($di) {
            $config = $di->get('config');
            $url = new UrlResolver();
            $url->setBaseUri($config->baseUri);

            return $url;
        });

        $di->set('session', function () {
            $session = new SessionAdapter();
            if (!$session->isStarted()) {
                //NOTICE: Get php warning here
                @$session->start();
            }

            return $session;
        });

        $di->set('cookies', function () {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);

            return $cookies;
        });


        $di->set('viewCache', function() use ($di) {
            $config = $di->get('config');

            $frontCacheClass = $config->cache->viewCache->frontend->adapter;
            $frontCacheClass = 'Phalcon\Cache\Frontend\\' . ucfirst($frontCacheClass);
            $frontCache = new $frontCacheClass(
                $config->cache->viewCache->frontend->options->toArray()
            );

            if(!$config->cache->enable || !$config->cache->viewCache) {
                $cache = new \Eva\EvaEngine\Cache\Backend\Disable($frontCache);
            } else {
                $backendCacheClass = $config->cache->viewCache->backend->adapter;
                $backendCacheClass = 'Phalcon\Cache\Backend\\' . ucfirst($backendCacheClass);
                $cache = new $backendCacheClass($frontCache, array_merge(
                    array(
                        'prefix' => 'eva_view_',
                    ),
                    $config->cache->viewCache->backend->options->toArray()
                ));
            }
            return $cache;
        });

        $di->set('modelCache', function() use ($di) {
            $config = $di->get('config');

            $frontCacheClass = $config->cache->modelCache->frontend->adapter;
            $frontCacheClass = 'Phalcon\Cache\Frontend\\' . ucfirst($frontCacheClass);
            $frontCache = new $frontCacheClass(
                $config->cache->modelCache->frontend->options->toArray()
            );

            if(!$config->cache->enable || !$config->cache->modelCache) {
                $cache = new \Eva\EvaEngine\Cache\Backend\Disable($frontCache);
            } else {
                $backendCacheClass = $config->cache->modelCache->backend->adapter;
                $backendCacheClass = 'Phalcon\Cache\Backend\\' . ucfirst($backendCacheClass);
                $cache = new $backendCacheClass($frontCache, array_merge(
                    array(
                        'prefix' => 'eva_model_',
                    ),
                    $config->cache->modelCache->backend->options->toArray()
                ));
            }
            return $cache;
        });

        $di->set('apiCache', function() use ($di) {
            $config = $di->get('config');

            $frontCacheClass = $config->cache->apiCache->frontend->adapter;
            $frontCacheClass = 'Phalcon\Cache\Frontend\\' . ucfirst($frontCacheClass);
            $frontCache = new $frontCacheClass(
                $config->cache->apiCache->frontend->options->toArray()
            );

            if(!$config->cache->enable || !$config->cache->apiCache) {
                $cache = new \Eva\EvaEngine\Cache\Backend\Disable($frontCache);
            } else {
                $backendCacheClass = $config->cache->apiCache->backend->adapter;
                $backendCacheClass = 'Phalcon\Cache\Backend\\' . ucfirst($backendCacheClass);
                $cache = new $backendCacheClass($frontCache, array_merge(
                    array(
                        'prefix' => 'eva_api_',
                    ),
                    $config->cache->apiCache->backend->options->toArray()
                ));
            }
            return $cache;
        });


        $di->set('view', function () use ($di) {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            $view->setEventsManager($di->get('eventsManager'));
            return $view;
        });

        $di->set('mailer', function () use ($di) {
            $config = $di->get('config');
            $transport = \Swift_SmtpTransport::newInstance()
            ->setHost($config->mailer->host)
            ->setPort($config->mailer->port)
            ->setEncryption($config->mailer->encryption)
                ->setUsername($config->mailer->username)
                ->setPassword($config->mailer->password)
            ;

            $mailer = \Swift_Mailer::newInstance($transport);

            return $mailer;
        });

        $di->set('mailMessage', 'Eva\EvaEngine\MailMessage');

        $di->set('queue', function () use ($di) {
            $config = $di->get('config');
            $client = new \GearmanClient();
            $client->setTimeout(1000);
            foreach ($config->queue->servers as $key => $server) {
                $client->addServer($server->host, $server->port);
            }

            return $client;
        });

        $di->set('worker', function () use ($di) {
            $config = $di->get('config');
            $worker = new \GearmanWorker();
            foreach ($config->queue->servers as $key => $server) {
                $worker->addServer($server->host, $server->port);
            }

            return $worker;
        });

        $di->set('flash', function () {
            $flash = new \Phalcon\Flash\Session();

            return $flash;
        });

        $di->set('escaper', function () {
            return new \Phalcon\Escaper();
        });

        $di->set('translate', function () use ($di) {
            $config = $di->get('config');
            $file = $config->translate->path . $config->translate->forceLang . '.csv';
            if (false === file_exists($file)) {
                $file = $config->translate->path . 'empty.csv';
            }
            $translate = new \Phalcon\Translate\Adapter\Csv(array(
                'file' => $file,
                'delimiter' => ',',
            ));

            return $translate;
        });

        $di->set('tag', function () use ($di) {
            \Eva\EvaEngine\Tag::setDi($di);

            return new \Eva\EvaEngine\Tag();
        });

        $di->set('placeholder', function(){
            return new \Eva\EvaEngine\View\Helper\Placeholder();
        }, true);

        $di->set('logException', function () use ($di) {
            $config = $di->get('config');

            return $logger = new FileLogger($config->logger->path . 'error_' . date('Y-m-d') . '.log');
        });

        $di->set('modelsMetadata', function () use ($di) {
            $config = $di->get('config');
            $metaData = new \Phalcon\Mvc\Model\Metadata\Files($config->modelsMetadata->options->toArray());

            return $metaData;
        });

        /*
        $di->set('db', function () use ($di) {
            $config = $di->get('config');
            $dbAdapter = new DbAdapter(array(
                'host' => $config->dbAdapter->master->host,
                'username' => $config->dbAdapter->master->username,
                'password' => $config->dbAdapter->master->password,
                'dbname' => $config->dbAdapter->master->database,
                'charset' => 'utf8',
            ));
            $eventsManager = new EventsManager();
            $logger = new FileLogger($config->logger->path . date('Y-m-d') . '.log');
            $eventsManager->attach('db', function ($event, $dbAdapter) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $sqlVariables = $dbAdapter->getSQLVariables();
                    if (count($sqlVariables)) {
                        $query = str_replace(array('%', '?'), array('%%', "'%s'"), $dbAdapter->getSQLStatement());
                        $query = vsprintf($query, $sqlVariables);
                        //
                        $logger->log($query, \Phalcon\Logger::INFO);
                    } else {
                        $logger->log($dbAdapter->getSQLStatement(), \Phalcon\Logger::INFO);
                    }
                }
            });

            $dbAdapter->setEventsManager($eventsManager);

            return $dbAdapter;
        });
        */

        $di->set('dispatcher', function () use ($di) {
            $eventsManager = $di->get('eventsManager');
            $dispatcher = new Dispatcher();
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        }, true);

        $di->set('dbMaster', function () use ($di) {
            $config = $di->get('config');

            $dbAdapter = new DbAdapter(array(
                'host' => $config->dbAdapter->master->host,
                'username' => $config->dbAdapter->master->username,
                'password' => $config->dbAdapter->master->password,
                'dbname' => $config->dbAdapter->master->database,
                'charset' => isset($config->dbAdapter->master->charset) ? $config->dbAdapter->master->charset : 'utf8',
            ));

            if ($config->debug) {
                $eventsManager = new EventsManager();
                $logger = new FileLogger($config->logger->path . date('Y-m-d') . '.log');

                //database service name hardcore as db
                $eventsManager->attach('db', function ($event, $dbAdapter) use ($logger) {
                    if ($event->getType() == 'beforeQuery') {
                        $sqlVariables = $dbAdapter->getSQLVariables();
                        if (count($sqlVariables)) {
                            $query = str_replace(array('%', '?'), array('%%', "'%s'"), $dbAdapter->getSQLStatement());
                            $query = vsprintf($query, $sqlVariables);
                            //
                            $logger->log($query, \Phalcon\Logger::INFO);
                        } else {
                            $logger->log($dbAdapter->getSQLStatement(), \Phalcon\Logger::INFO);
                        }
                    }
                });
                $dbAdapter->setEventsManager($eventsManager);
            }

            return $dbAdapter;
        });

        $di->set('modelsManager', function () use ($di) {
            //for solving db master/slave under static find method
            $modelsManager = new \Eva\EvaEngine\Mvc\Model\Manager();

            return $modelsManager;
        });

        $di->set('dbSlave', function () use ($di) {
            $config = $di->get('config');
            $slaves = $config->dbAdapter->slave;
            $slaveKey = array_rand($slaves->toArray());
            $dbAdapter = new DbAdapter(array(
                'host' => $config->dbAdapter->slave->$slaveKey->host,
                'username' => $config->dbAdapter->slave->$slaveKey->username,
                'password' => $config->dbAdapter->slave->$slaveKey->password,
                'dbname' => $config->dbAdapter->slave->$slaveKey->database,
                'charset' => isset($config->dbAdapter->slave->$slaveKey->charset) ? $config->dbAdapter->slave->$slaveKey->charset : 'utf8',
            ));

            if ($config->debug) {
                $eventsManager = new EventsManager();
                $logger = new FileLogger($config->logger->path . date('Y-m-d') . '.log');
                $eventsManager->attach('db', function ($event, $dbAdapter) use ($logger) {
                    if ($event->getType() == 'beforeQuery') {
                        $sqlVariables = $dbAdapter->getSQLVariables();
                        if (count($sqlVariables)) {
                            $query = str_replace(array('%', '?'), array('%%', "'%s'"), $dbAdapter->getSQLStatement());
                            $query = vsprintf($query, $sqlVariables);
                            //
                            $logger->log($query, \Phalcon\Logger::INFO);
                        } else {
                            $logger->log($dbAdapter->getSQLStatement(), \Phalcon\Logger::INFO);
                        }
                    }
                });
                $dbAdapter->setEventsManager($eventsManager);
            }

            return $dbAdapter;
        });



        /*
        $di->set('fileSystem', function () use ($di) {
            $config = $di->get('config');
            $adapter = new \Gaufrette\Adapter\Local();
            $filesystem = new Filesystem($adapter);

            return $filesystem;
        });
        */

        return $this->di = $di;
    }

    public function setModulesPath($modulesPath)
    {
        $this->modulesPath = $modulesPath;

        return $this;
    }

    public function getModulesPath()
    {
        if ($this->modulesPath) {
            return $this->modulesPath;
        }

        return $this->modulesPath = $this->appRoot . '/modules';
    }

    public function loadModules(array $modules)
    {
        $moduleArray = array();
        $modulesPath = $this->getModulesPath();

        foreach ($modules as $key => $module) {
            if (is_array($module)) {
                if (!isset($module['className'])) {
                    $module['className'] = "Eva\\$key\\Module";
                }
                if (!isset($module['path'])) {
                    $module['path'] = "$modulesPath/$key/Module.php";
                }
                $moduleArray[$key] = $module;
            } elseif (is_string($module)) {
                //Only Module Name means its a Eva Standard module
                $moduleArray[$module] = array(
                    'className' => "Eva\\$module\\Module",
                    'path' => "$modulesPath/$module/Module.php",
                );
            } else {
                throw new \Exception('Module not load by incorrect format');
            }
        }

        $application = $this->getApplication();
        $application->registerModules($moduleArray);

        $modules = $application->getModules();
        $loader = new Loader();
        $loaderArray = array();
        foreach ($moduleArray as $module) {
            $loaderArray[$module['className']] = $module['path'];
        }
        $loader->registerClasses($loaderArray)->register();
        $loaderArray = array();
        foreach ($moduleArray as $module) {
            $moduleLoader = method_exists($module['className'], 'registerGlobalAutoloaders') ?
            $module['className']::registerGlobalAutoloaders() :
            array();
            if ($moduleLoader instanceof $loader) {
                continue;
            }
            $loaderArray += $moduleLoader;
        }
        if ($loaderArray) {
            $loader->registerNamespaces($loaderArray)->register();
        }

        $di = $this->getDI();
        $di->set('moduleManager', function () use ($moduleArray) {
            return new ModuleManager($moduleArray);
        });

        return $this;
    }

    public function bootstrap()
    {
        $this->getApplication()->setDI($this->getDI());
        //Error Handler must run before router start
        $this->initErrorHandler(new Error\ErrorHandler);

        return $this;
    }

    public function runCustom()
    {
        $di = $this->getDI();

        $debug = $di->get('config')->debug;
        if ($debug) {
            $debugger = new \Phalcon\Debug();
            $debugger->listen();
        }

        //Roter
        $router = $di['router'];
        $router->handle();

        //Module handle
        $modules = $this->getApplication()->getModules();
        $routeModule = $router->getModuleName();
        if (isset($modules[$routeModule])) {
            $moduleClass = new $modules[$routeModule]['className']();
            $moduleClass->registerAutoloaders();
            $moduleClass->registerServices($di);
        }

        //dispatch
        $dispatcher = $di['dispatcher'];
        $dispatcher->setModuleName($router->getModuleName());
        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());

        //view
        $view = $di['view'];
        $view->start();
        $controller = $dispatcher->dispatch();
        //Not able to call render in controller or else will repeat output
        $view->render(
            $dispatcher->getControllerName(),
            $dispatcher->getActionName(),
            $dispatcher->getParams()
        );
        $view->finish();

        //NOTICE: not able to output flash session content
        $response = $di['response'];
        $response->setContent($view->getContent());
        $response->sendHeaders();
        echo $response->getContent();
    }

    public function run()
    {
        $di = $this->getDI();

        $debug = $di->get('config')->debug;
        if ($debug) {
            $debugger = new \Phalcon\Debug();
            $debugger->debugVar($this->getApplication()->getModules(), 'modules');
            $debugger->listen(true, true);
        }

        $response = $this->getApplication()->handle();
        echo $response->getContent();
    }

    public function __construct($appRoot = null)
    {
        $this->appRoot = $appRoot ? $appRoot : __DIR__;
    }

}
