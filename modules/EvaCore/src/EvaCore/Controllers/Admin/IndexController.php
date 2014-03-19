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
        $view->setTemplateAfter('login');
        $view->pick('index/index');
    }


    public function dashboardAction()
    {
        $view = $this->view;
        $view->setViewsDir($this->view->getViewsDir() . '_admin/');
        $view->setLayoutsDir('layouts/');
        $view->setLayout('admin');
        $view->setTemplateAfter('admin');
        $view->pick('index/dashboard');
    }
}
