<?php

namespace Eva\EvaCore\Controllers\Admin;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class IndexController extends Controller 
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $view = $this->view;
        $view->setViewsDir($this->view->getViewsDir() . '_admin/');
        $view->setLayoutsDir('layouts/');
        $view->setLayout('login');
        /*
        $view->setTemplateAfter('admin');
        $view->setVars(array(
            'title' => 'abc'
        ));
        $view->pick('index/index');
        */
    }

}
