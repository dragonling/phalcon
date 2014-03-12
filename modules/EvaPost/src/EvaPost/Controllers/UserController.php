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
        $module = new \Eva\EvaPost\Module();
        p($module);
        $user = new \Eva\EvaUser\Entities\Users();
        p('post');
    }

}
