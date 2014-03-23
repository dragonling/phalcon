<?php

namespace Eva\EvaUser\Controllers;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $view = $this->view;
        $view->setViewsDir($this->getDI()->get('modules')->getModulePath('EvaCore') . '/views/_admin/');
        $view->setLayoutsDir('layouts/');
        $view->setLayout('login');
    }

}
