<?php

namespace Eva\EvaBlog\Components;


use Eva\EvaBlog\Model;
use Eva\EvaBlog\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Post extends \Phalcon\Mvc\User\Component
{
    public function request($location, $data = null)
    {
        $dispatcher = clone $this->getDI()->get('dispatcher');

        if (isset($location['namespace'])) {
            $dispatcher->setNamespaceName($location['namespace']);
        }

        if (isset($location['controller'])) {
            $dispatcher->setControllerName($location['controller']);
        } else {
            $dispatcher->setControllerName('index');
        }

        if (isset($location['action'])) {
            $dispatcher->setActionName($location['action']);
        } else {
            $dispatcher->setActionName('index');
        }

        if (isset($location['params'])) {
            if(is_array($location['params'])) {
                $dispatcher->setParams($location['params']);
            } else {
                $dispatcher->setParams((array) $location['params']);
            }
        } else {
            $dispatcher->setParams(array());
        }

        $dispatcher->dispatch();

        $response = $dispatcher->getReturnedValue();
        if ($response instanceof ResponseInterface) {
            return $response->getContent();
        }

        return $response;
    }

    public function getPostList()
    {
        return $this->request(array(
            'namespace' => 'Eva\EvaBlog\Controllers',
            'controller' => 'post',
            'action' => 'list',
        ));
    }
}
