<?php

namespace Eva\EvaEngine\Error;

use Phalcon\DI;
use Phalcon\Logger\Adapter\File as FileLogger;

class ErrorHandler implements ErrorHandlerInterface
{
    protected static $errorController = 'error';

    protected static $errorControllerNamespace = 'Eva\EvaEngine\Mvc\Controller';

    protected static $errorControllerAction = 'index';

    protected static $errorLayout;

    protected static $errorTemplate;

    protected static $logger = false;

    //protected static $errorLevel

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!($errno & error_reporting())) {
            return;
        }

        $options = array(
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'isError' => true,
        );

        self::errorProcess(new Error($options));
    }

    public static function exceptionHandler(\Exception $e)
    {
        $options = array(
            'type' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'isException' => true,
            'exception' => $e,
        );

        self::errorProcess(new Error($options));
    }

    public static function shutdownHandler()
    {
        if (!is_null($options = error_get_last())) {
            self::errorProcess(new Error($options));
        }
    }

    public static function setErrorController($controller)
    {
        self::$errorController = $controller;
    }

    public static function getErrorController()
    {
        return self::$errorController;
    }

    public static function setErrorControllerNamespace($controllerNamespace)
    {
        self::$errorControllerNamespace = $controllerNamespace;
    }

    public static function getErrorControllerNamespace()
    {
        return self::$errorControllerNamespace;
    }

    public static function setErrorControllerAction($action)
    {
        self::$errorControllerAction = $action;
    }

    public static function getErrorControllerAction()
    {
        return self::$errorControllerAction;
    }

    public static function setErrorLayout()
    {
    }

    public static function setErrorTemplate()
    {
    }

    public static function getLogger()
    {
        if (self::$logger !== false) {
            return self::$logger;
        }

        $di = DI::getDefault();
        $config = $di->get('config');

        if(!isset($config->error->disableLog) ||
            (isset($config->error->disableLog) && $config->error->disableLog) ||
            empty($config->error->logPath)
        ) {
            return self::$logger = null;
        }

        self::$logger = new FileLogger($config->error->logPath . '/' . 'system_error_' . date('Ymd') . '.log');

        return self::$logger;
    }

    protected static function logError(Error $error)
    {
        $logger = self::getLogger();
        if (!$logger) {
            return;
        }

        return $logger->log($error);
    }

    protected static function errorProcess(Error $error)
    {
        self::logError($error);
        $useErrorController = false;

        if ($error->isException()) {
            $useErrorController = true;
        } else {
            switch ($error->type()) {
                case E_WARNING:
                case E_NOTICE:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
                case E_USER_NOTICE:
                case E_STRICT:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                case E_ALL:
                break;
                default:
                $useErrorController = true;
            }
        }

        if (!$useErrorController) {
            return;
        }

        $di = DI::getDefault();
        $dispatcher = $di->getShared('dispatcher');
        $view = $di->getShared('view');
        $response = $di->getShared('response');
        $response->setStatusCode($error->statusCode(), $error->statusMessage());

        $dispatcher->setNamespaceName(self::getErrorControllerNamespace());
        $dispatcher->setControllerName(self::getErrorController());
        $dispatcher->setActionName(self::getErrorControllerAction());
        $dispatcher->setParams(array('error' => $error));

        $view->start();
        $dispatcher->dispatch();
        $view->render();
        $view->finish();

        return $response->setContent($view->getContent())->send();
    }
}
