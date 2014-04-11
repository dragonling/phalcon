<?php

namespace Eva\EvaBlog\Controllers\Admin;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $view = $this->view;
        $view->setViewsDir($this->getDI()->get('modules')->getModulePath('EvaCore') . '/views/_admin/');
        $view->setLayoutsDir('layouts/');
        $view->setLayout('admin');
    }
}
