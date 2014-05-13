<?php

namespace Eva\EvaEngine\Error;

use Phalcon\DI;

class ErrorHandler implements ErrorHandlerInterface
{
    protected static $errorController = 'error';

    protected static $errorControllerNamespace = 'Eva\EvaEngine\Mvc\Controller';

    protected static $errorControllerAction = 'index';

    protected static $errorLayout;

    protected static $errorTemplate;

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

    protected static function errorProcess(Error $error)
    {
        $di = DI::getDefault();
        $config = $di->getShared('config');
        $type = $error->errorType();
        $message = "$type: {$error->message()} in {$error->file()} on line {$error->line()}";

        $useErrorController = false;
        //$config->error->logger->log($message);

        if($error->isException()) {
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

        if(!$useErrorController) {
            return;
        }

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
        //$view->render($config->error->controller, $config->error->action, $dispatcher->getParams());
        $view->render();
        $view->finish();

        return $response->setContent($view->getContent())->send();
    }
}
