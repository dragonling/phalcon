<?php

namespace Eva\EvaEngine\Controller;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function errorHandler($exception, $messages = null, $messageType = 'error')
    {
        if($messages) {
            foreach($messages as $message) {
                $this->flashSession->$messageType($message->getMessage());
            }
        }

        //Not eva exception, keep throw
        if(!($exception instanceof \Eva\EvaEngine\Exception\ExceptionInterface)){
            throw $exception;
        }
        $this->response->setStatusCode($exception->getStatusCode(), $exception->getMessage());
        $this->flashSession->$messageType($exception->getMessage());
        //write log here
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

