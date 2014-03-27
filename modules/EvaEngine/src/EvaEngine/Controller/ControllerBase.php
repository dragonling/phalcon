<?php

namespace Eva\EvaEngine\Controller;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function ignoreHandler($exception, $messages = null, $messageType = 'debug')
    {
        $messageArray = array();
        if($messages) {
            foreach($messages as $message) {
                $messageArray[] = $message->getMessage();
            }
        }

        $logger = $this->getDI()->get('logException');
        $logger->debug($exception);
        /*
        $logger->debug(
            implode('', $messageArray) . "\n" .
            get_class($exception) . ":" . $exception->getMessage(). "\n" .
            " File=" . $exception->getFile() . "\n" .
            " Line=", $exception->getLine() . "\n" .
            $exception->getTraceAsString()
        );
        */
    
    }


    public function errorHandler($exception, $messages = null, $messageType = 'error')
    {
        $messageArray = array();
        if($messages) {
            foreach($messages as $message) {
                $this->flashSession->$messageType($message->getMessage());
                $messageArray[] = $message->getMessage();
            }
        }

        $logger = $this->getDI()->get('logException');
        $logger->log(
            implode('', $messageArray) . "\n" .
            get_class($exception) . ":" . $exception->getMessage(). "\n" .
            " File=" . $exception->getFile() . "\n" .
            " Line=", $exception->getLine() . "\n" .
            $exception->getTraceAsString()
        );

        //Not eva exception, keep throw
        if(!($exception instanceof \Eva\EvaEngine\Exception\ExceptionInterface)){
            throw $exception;
        }

        $this->response->setStatusCode($exception->getStatusCode(), $exception->getMessage());
        $this->flashSession->$messageType($exception->getMessage());

        return $this;
    }

    public function validHandler(\Phalcon\Forms\Form $form, $messageType = 'warning')
    {
        $messages = $form->getMessages();
        if($messages) {
            foreach($messages as $message) {
                $this->flashSession->$messageType($message->getMessage());
            }
        }
        return $this;
    }

}

