<?php

namespace Eva\EvaUser\Controllers\Admin;


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        //$this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_LAYOUT);
        //$this->view->setLayoutsDir('D:\xampp\htdocs\phalcon\modules\EvaCore\layouts');
        $this->view->setLayout('admin');
        $this->view->setViewsDir($this->view->getViewsDir() . '_admin/');

        $this->view->setTemplateAfter('admin');
        $this->view->setVars(array(
            'title' => 'abc'
        ));
        $this->view->pick('user/index');
    }

}
