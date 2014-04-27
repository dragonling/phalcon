<?php

namespace Eva\EvaBlog\Components;


use Eva\EvaBlog\Model;
use Eva\EvaBlog\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Post extends \Eva\EvaEngine\Mvc\User\Component
{
    public function __invoke($params)
    {
        return $this->reDispatch(array(
            'module' => 'EvaBlog',
            'namespace' => 'Eva\EvaBlog\Controllers',
            'controller' => 'post',
            'action' => 'list',
            'params' => $params,
        ));
    }
}
