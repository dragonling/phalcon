<?php

namespace Eva\EvaPost\Controllers;


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $user = new \Eva\EvaUser\Entities\Users();
        p('post');
    }

}
